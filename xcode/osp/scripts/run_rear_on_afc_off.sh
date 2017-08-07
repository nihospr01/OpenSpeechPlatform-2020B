#!/bin/sh

IP_ADDR=`ipconfig getifaddr en0`

echo "============================================"
echo "Connect tablet to ip address: $IP_ADDR      "
echo "============================================"

/opt/osp/osp -a -t
