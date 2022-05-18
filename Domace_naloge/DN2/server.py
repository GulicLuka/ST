"""An example of a simple HTTP server."""
import json
import mimetypes
import pickle
import socket
from os import listdir
from os.path import isdir, isfile, join
from urllib.parse import unquote_plus

# Pickle file for storing data
PICKLE_DB = "db.pkl"

# Directory containing www data
WWW_DATA = "www-data"

# Header template for a successful HTTP request
HEADER_RESPONSE_200 = """HTTP/1.1 200 OK\r
content-type: %s\r
content-length: %d\r
connection: Close\r
\r
"""

# Represents a table row that holds user data
TABLE_ROW = """
<tr>
    <td>%d</td>
    <td>%s</td>
    <td>%s</td>
</tr>
"""
# Template for a 301 (Moved Permanently) error
RESPONSE_301 = """HTTP/1.1 301 Moved Permanently\r
location: %s\r
connection: Close\r
\r
"""

RESPONSE_400 = """HTTP/1.1 400 Bad Request
content-type: text/html\r
connection: Close\r
\r
<!doctype html>
<h1>400 Bad Request</h1>
"""

# Template for a 404 (Not found) error
RESPONSE_404 = """HTTP/1.1 404 Not found\r
content-type: text/html\r
connection: Close\r
\r
<!doctype html>
<h1>404 Page not found</h1>
<p>Page cannot be found.</p>
"""

RESPONSE_405 = """HTTP/1.1 405 not allowed\r
content-type: text/html\r
connection: Close\r
\r
<!doctype html>
<h1>405 Method Not Allowed</h1>
<p>Method is not allowed.</p>
"""

DIRECTORY_LISTING = """<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
<title>Directory listing: %s</title>

<h1>Contents of %s:</h1>

<ul>
{{CONTENTS}}
</ul> 
"""

FILE_TEMPLATE = "  <li><a href='%s'>%s</li>"


def save_to_db(first, last):
    """Create a new user with given first and last name and store it into
    file-based database.

    For instance, save_to_db("Mick", "Jagger"), will create a new user
    "Mick Jagger" and also assign him a unique number.

    Do not modify this method."""

    existing = read_from_db()
    existing.append({
        "number": 1 if len(existing) == 0 else existing[-1]["number"] + 1,
        "first": first,
        "last": last
    })
    with open(PICKLE_DB, "wb") as handle:
        pickle.dump(existing, handle)


def read_from_db(criteria=None):
    """Read entries from the file-based DB subject to provided criteria

    Use this method to get users from the DB. The criteria parameters should
    either be omitted (returns all users) or be a dict that represents a query
    filter. For instance:
    - read_from_db({"number": 1}) will return a list of users with number 1
    - read_from_db({"first": "bob"}) will return a list of users whose first
    name is "bob".

    Do not modify this method."""
    if criteria is None:
        criteria = {}
    else:
        # remove empty criteria values
        for key in ("number", "first", "last"):
            if key in criteria and criteria[key] == "":
                del criteria[key]

        # cast number to int
        if "number" in criteria:
            criteria["number"] = int(criteria["number"])

    try:
        with open(PICKLE_DB, "rb") as handle:
            data = pickle.load(handle)

        filtered = []
        for entry in data:
            predicate = True

            for key, val in criteria.items():
                if val != entry[key]:
                    predicate = False

            if predicate:
                filtered.append(entry)

        return filtered
    except (IOError, EOFError):
        return []

def parse_headers(client):
    headers = dict()
    while True:
        line = client.readline().decode("utf-8").strip()
        if not line:
            return headers
        key, value = line.split(":", 1)
        headers[key.strip()] = value.strip()

def process_request(connection, address, port):
    """Process an incoming socket request.

    :param connection is a socket of the client
    :param address is a 2-tuple (address(str), port(int)) of the client
    """

    # Read and parse the request line
    client = connection.makefile("wrb")

    line = client.readline().decode("utf-8").strip()
    try: 
        if len(line.split()) == 3 and line.split()[2] == "HTTP/1.1":
            method, uri, version = line.split()
            assert method == "GET" or method == "POST"

            if method == "GET":
                parsed_uri = unquote_plus(uri)
                if(len(parsed_uri) > 0 and parsed_uri[0] == "/"):
                    if parsed_uri != "/app-add":
                        #DINSMICNO HTML
                        if "/app-index" in parsed_uri:
                            if "?" in parsed_uri:
                                params = parsed_uri[parsed_uri.index("?") + 1:]
                                paramsDict = dict()
                                for x in params.split("&"):
                                    xs = x.split("=")
                                    if xs[0] in ["first", "last", "number"] and xs[1].strip() != "":
                                        paramsDict[xs[0]] = xs[1]
                                data = read_from_db(paramsDict)
                            else:
                                data = read_from_db()
                            rows = []
                            for r in data:
                                rows.append(TABLE_ROW % (r["number"], r["first"], r["last"]))
                            concatinatedRows = "\n".join(rows)
                            with open(WWW_DATA + "/app_list.html", "rb") as handle:
                                body = handle.read()
                            body = body.decode("utf-8").replace("{{students}}", concatinatedRows)
                            header = HEADER_RESPONSE_200 % ("text/html", len(body))
                            client.write(header.encode("utf-8"))
                            client.write(body.encode("utf-8"))
                        #DINAMICNO JSON
                        elif "/app-json" in parsed_uri:
                            if "?" in parsed_uri:
                                params = parsed_uri[parsed_uri.index("?") + 1:]
                                paramsDict = dict()
                                for x in params.split("&"):
                                    xs = x.split("=")
                                    if xs[0] in ["first", "last", "number"] and xs[1].strip() != "":
                                        paramsDict[xs[0]] = xs[1]
                                data = read_from_db(paramsDict)                  
                            else:
                                data = read_from_db()
  
                            body = json.dumps(data)

                            header = HEADER_RESPONSE_200 % ("application/json", len(body))
                            client.write(header.encode("utf-8"))
                            client.write(body.encode("utf-8"))

                        #STATICNO
                        else:
                            #PRIMER Z ZAKLJUCENO POSEVNICO
                            if parsed_uri[-1] == "/":
                                if isfile(WWW_DATA + parsed_uri + "index.html"):
                                    with open(WWW_DATA + parsed_uri + "index.html", "rb") as handle:
                                        body = handle.read()
                                    
                                    mime_type, _ = mimetypes.guess_type(WWW_DATA + parsed_uri + "index.html")

                                    header = HEADER_RESPONSE_200 % (mime_type, len(body))
                                    client.write(header.encode("utf-8"))
                                    client.write(body)

                                elif isdir(WWW_DATA + parsed_uri):
                                    files = listdir(WWW_DATA + parsed_uri)
                                    arrTameplates = [FILE_TEMPLATE % ("..", "..")]
                                    for file in sorted(files):
                                        arrTameplates.append(FILE_TEMPLATE % (file, file))

                                    concatinated = "\n".join(arrTameplates)

                                    body = DIRECTORY_LISTING % (parsed_uri, parsed_uri)
                                    body = body.replace("{{CONTENTS}}", concatinated)

                                    header = HEADER_RESPONSE_200 % ("text/html", len(body))
                                    client.write(header.encode("utf-8"))
                                    client.write(body.encode("utf-8"))
                                else:
                                    client.write(RESPONSE_404.encode("utf-8"))

                            else:
                                #PRIMER BREZ ZAKLJUCNE POSEVNICE
                                if isfile(WWW_DATA + parsed_uri):
                                    with open(WWW_DATA + parsed_uri, "rb") as handle:
                                        body = handle.read()
                                    
                                    mime_type, _ = mimetypes.guess_type(WWW_DATA + parsed_uri)
                                    if mime_type != None:
                                        header = HEADER_RESPONSE_200 % (mime_type, len(body))
                                        client.write(header.encode("utf-8"))
                                        client.write(body)
                                    else:
                                        header = HEADER_RESPONSE_200 % ("application/octet-stream", len(body))
                                        client.write(header.encode("utf-8"))
                                        client.write(body)

                                elif isdir(WWW_DATA + parsed_uri):
                                    full_url = "http://localhost:%d%s" % (port, parsed_uri + "/")
                                    header = RESPONSE_301 % (full_url)
                                    client.write(header.encode("utf-8"))

                                else:
                                    client.write(RESPONSE_404.encode("utf-8"))
                    else:
                        client.write(RESPONSE_405.encode("utf-8"))
                else:
                    client.write(RESPONSE_400.encode("utf-8"))                        
            #-----------------------------------------------POST------------------------------------------------------------------
            elif method == "POST":
                parsed_uri = unquote_plus(uri)
                if(len(parsed_uri) > 0 and parsed_uri[0] == "/"):
                    if parsed_uri == "/app-add" :
                        headers = parse_headers(client)
                        input_line = client.read(int(headers["Content-Length"]))
                        inputDict = dict()
                        
                        input_line = input_line.decode("utf-8")
                        input_line = unquote_plus(input_line)
                        
                        for x in input_line.split("&"):
                            xs = x.split("=")
                            if xs[0] in ["first", "last"] and xs[1].strip() != "":
                                inputDict[xs[0]] = xs[1]

                        if "first" in inputDict.keys() and "last" in inputDict.keys() and inputDict["first"] != "" and inputDict["last"] != "":
                            save_to_db(inputDict["first"], inputDict["last"])

                            with open(WWW_DATA + "/app_add.html", "rb") as handle:
                                body = handle.read()
                            
                            mime_type, _ = mimetypes.guess_type(WWW_DATA + "/app_add.html")

                            header = HEADER_RESPONSE_200 % (mime_type, len(body))
                            client.write(header.encode("utf-8"))
                            client.write(body)
                        else:
                            #RESPONSE_400
                            client.write(RESPONSE_400.encode("utf-8"))                        
                    elif "/app-index" in parsed_uri or "/app-json" in parsed_uri:
                        client.write(RESPONSE_405.encode("utf-8"))
                    else:
                        client.write(RESPONSE_400.encode("utf-8"))
        else:
            client.write(RESPONSE_400.encode("utf-8"))
    except AssertionError as e:
        client.write(RESPONSE_405.encode("utf-8"))
    except FileNotFoundError as e:
        client.write(RESPONSE_404.encode("utf-8"))

    client.close()

def main(port):
    """Starts the server and waits for connections."""

    server = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    server.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    server.bind(("", port))
    server.listen(1)

    print("Listening on %d" % port)

    while True:
        connection, address = server.accept()
        print("[%s:%d] CONNECTED" % address)
        process_request(connection, address, port)
        connection.close()
        print("[%s:%d] DISCONNECTED" % address)


if __name__ == "__main__":
    main(8080)
