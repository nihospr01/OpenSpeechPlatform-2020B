# Fake (Test) Client and Server

This directory includes a fake client and server
using [ZeroMQ](https://zeromq.org/).  Its purpose
is to assist in testing while switching to the new ZeroMQ-based osp.

The nodejs and php web servers need to be updated to use ZeroMQ.

## Previous Implementation

The previous implementation used TCP on port 8001.  The osp process
would wait on incoming connections on port 8001.  When a connection
was made, it would read the available data and then respond.  Then it
would terminate the connection and go back into the wait state.  

It returned json data in response to a message of

```json
{"method": "get"}
```

and it returned a string of "success" in response when it received

```json
{"method": "set", "data" : {...}}
```

Otherwise it terminated the connection without sending anything.

## New ZeroMQ-based OSP

I've kept it as similar as possible.

ZeroMQ is also using TCP port 8001.  However it won't be terminating the connection on every message.  It will always respons to each incoming message with either "success"  or "FAILED".  Then will resume waiting for messages.

ZeroMQ is widely used in the software industry and there are plenty of examples of how to use it with nodejs and php.  

osp_fake_client.py shows a standalone python client.

(Note that the embedded web servers will act as a client to the osp process which is serving the osp data.)

The EWS code should initialize the ZeroMQ context and open a socket.  When it needs data from `osp`, it sends a message to `osp` and returns the response.  Some thought should be given to handling failures; when `osp` returns "FAILED" or when the connection times out.  How you handle that depends on what is currently implemented for each server.  

## Getting Started

Create a branch from develop to work on.  Maybe a good time to learn GitFlow.  "git flow feature start zeromq"

Install the python ZeroMQ bindings.

    pip install pyzmq
    - or -
    sudo apt install python3-zmq

Run osp_fake_server.py in a terminal.

- Install the nodejs or php ZeroMQ bindings.  
- Modify the webserver code
- Watch the messages from the osp_fake_server terminal

You should be able to get web apps and demos "working" using the
fake server.  There won't be any sound (because there is no `osp`)and variables you set won't actually be set.

When both nodejs and php servers are ready we will merge our changes into develop for those and osp.

## Why?

ZeroMQ is

- Fast
- Flexible
- Widely used
- Small

We could remove the large Poco::Net code from OSP and write our own TCP code, but why do that when ZeroMQ is easier and gives us so many advantages?
