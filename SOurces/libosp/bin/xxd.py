#!/usr/bin/env python3

# like "xxd" but better

import sys
import binascii
import argparse

def main(fname, vname):

    with open(fname, 'r') as fp:
        buff2 = binascii.hexlify(fp.read().encode()).decode()
        print(f"unsigned char {vname}[] = {{")
        obytes = "0x"+", 0x".join([buff2[i:i+2] for i in range(0,len(buff2),2)])
        obytes = obytes.split(',')
        
        cnt = 0
        while cnt < len(obytes):
            print(f" {obytes[cnt]},", end="")
            cnt += 1;
            if cnt % 12 == 0: print()

        print(f"\n}};\nunsigned {vname}_len = {len(obytes)};\n")

if __name__ == '__main__':
    parser = argparse.ArgumentParser(prog='xxd')
    parser.add_argument('-f', '--file', help='Filename')
    parser.add_argument('-v', '--var', help='Variable name')
    args = parser.parse_args()
    sys.exit(main(args.file, args.var))