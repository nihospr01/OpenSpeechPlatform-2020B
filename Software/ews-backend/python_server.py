#!/usr/bin/env python3 
import socket
from random import random
import json

HOST = '127.0.0.1'  # Standard loopback interface address (localhost)
PORT = 6000         # Port to listen on (non-privileged ports are > 1023)
s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)


with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
    s.bind((HOST, PORT))
    s.listen()
    #while connected:
    while True:
        conn, addr = s.accept()
        with conn:
            print('Connected by', addr)
            while True:
                
                data = conn.recv(1024)
                if data:
                    time = {"data":random() * 10000}
                    time = json.dumps(time)
                    conn.sendall(time.encode())
                    conn.close()
                    break

           
    # finally:
    #     # Clean up the connection
    #     conn.close()
    # s.bind((HOST, PORT))
    # s.listen()
    # conn, addr = s.accept()
    # with conn:
    #     print('Connected by', addr)
    #     while True:
    #         data = conn.recv(1024)
    #         time = {"data":random()}
    #         time = json.dumps(time)
    #         conn.sendall(time.encode())