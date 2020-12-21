#!/usr/bin/env python

"""
A simple client that connects to a ZeroMQ
server on port 8001.  It simply sends whatever
is input and then prints the response.
"""

import zmq
# pip install pyzmq
# sudo apt install python3-zmq


# Creates a new socket and sets
# the timeout to 5 seconds. Timeouts
# should only happen if the server is not running.
def open_socket(context):
    sock = context.socket(zmq.REQ)
    sock.connect("tcp://localhost:8001")
    sock.setsockopt(zmq.RCVTIMEO, 5000)
    sock.setsockopt(zmq.LINGER, 0)
    return sock

def client():
    context = zmq.Context()
    sock = open_socket(context)
    while True:
        # prompt for a string then send it
        msg = input("OSP > ")
        sock.send_string(msg)
        try:
            message = sock.recv().decode()
            print(message)
        except:
            print("Timed out.")
            sock = open_socket(context)


if __name__ == '__main__':
    client()