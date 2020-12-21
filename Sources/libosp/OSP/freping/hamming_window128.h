//======================================================================================================================
/** @file hamming_window128.h
 *  @author Open Speech Platform (OSP) Team, UCSD
 *  @copyright Copyright (C) 2020 Regents of the University of California Redistribution and use in source and binary
 *  forms, with or without modification, are permitted provided that the following conditions are met:
 *
 *      1. Redistributions of source code must retain the above copyright notice, this list of conditions and the
 *      following disclaimer.
 *
 *      2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the
 *      following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 *  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 *  INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 *  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 *  SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 *  SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 *  WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE
 *  USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
//======================================================================================================================

/* Hamming window with length 128 */

/* 
** This file is auto generated using the script mat2doth.m 
** You can #include this file in c/c++ code to compile-in 
** ROM data generated in MATLAB.
** Created by User:jason; 	 Date & Time:09-Jan-2020 21:29:55; 
*/ 

#ifndef hamming_window128_h 
#define hamming_window128_h

long hamming_window128_length = 128; 
static float hamming_window128[128] = 
{
	 +8.0000000000e-02f,
	 +8.0554090146e-02f,
	 +8.2215025731e-02f,
	 +8.4978805416e-02f,
	 +8.8838771015e-02f,
	 +9.3785623531e-02f,
	 +9.9807445563e-02f,
	 +1.0688973002e-01f,
	 +1.1501541504e-01f,
	 +1.2416492516e-01f,
	 +1.3431621840e-01f,
	 +1.4544483940e-01f,
	 +1.5752397834e-01f,
	 +1.7052453552e-01f,
	 +1.8441519145e-01f,
	 +1.9916248234e-01f,
	 +2.1473088065e-01f,
	 +2.3108288077e-01f,
	 +2.4817908928e-01f,
	 +2.6597831993e-01f,
	 +2.8443769281e-01f,
	 +3.0351273767e-01f,
	 +3.2315750106e-01f,
	 +3.4332465702e-01f,
	 +3.6396562111e-01f,
	 +3.8503066744e-01f,
	 +4.0646904846e-01f,
	 +4.2822911724e-01f,
	 +4.5025845187e-01f,
	 +4.7250398175e-01f,
	 +4.9491211545e-01f,
	 +5.1742886981e-01f,
	 +5.4000000000e-01f,
	 +5.6257113019e-01f,
	 +5.8508788455e-01f,
	 +6.0749601825e-01f,
	 +6.2974154813e-01f,
	 +6.5177088276e-01f,
	 +6.7353095154e-01f,
	 +6.9496933256e-01f,
	 +7.1603437889e-01f,
	 +7.3667534298e-01f,
	 +7.5684249894e-01f,
	 +7.7648726233e-01f,
	 +7.9556230719e-01f,
	 +8.1402168007e-01f,
	 +8.3182091072e-01f,
	 +8.4891711923e-01f,
	 +8.6526911935e-01f,
	 +8.8083751766e-01f,
	 +8.9558480855e-01f,
	 +9.0947546448e-01f,
	 +9.2247602166e-01f,
	 +9.3455516060e-01f,
	 +9.4568378160e-01f,
	 +9.5583507484e-01f,
	 +9.6498458496e-01f,
	 +9.7311026998e-01f,
	 +9.8019255444e-01f,
	 +9.8621437647e-01f,
	 +9.9116122899e-01f,
	 +9.9502119458e-01f,
	 +9.9778497427e-01f,
	 +9.9944590985e-01f,
	 +1.0000000000e+00f,
	 +9.9944590985e-01f,
	 +9.9778497427e-01f,
	 +9.9502119458e-01f,
	 +9.9116122899e-01f,
	 +9.8621437647e-01f,
	 +9.8019255444e-01f,
	 +9.7311026998e-01f,
	 +9.6498458496e-01f,
	 +9.5583507484e-01f,
	 +9.4568378160e-01f,
	 +9.3455516060e-01f,
	 +9.2247602166e-01f,
	 +9.0947546448e-01f,
	 +8.9558480855e-01f,
	 +8.8083751766e-01f,
	 +8.6526911935e-01f,
	 +8.4891711923e-01f,
	 +8.3182091072e-01f,
	 +8.1402168007e-01f,
	 +7.9556230719e-01f,
	 +7.7648726233e-01f,
	 +7.5684249894e-01f,
	 +7.3667534298e-01f,
	 +7.1603437889e-01f,
	 +6.9496933256e-01f,
	 +6.7353095154e-01f,
	 +6.5177088276e-01f,
	 +6.2974154813e-01f,
	 +6.0749601825e-01f,
	 +5.8508788455e-01f,
	 +5.6257113019e-01f,
	 +5.4000000000e-01f,
	 +5.1742886981e-01f,
	 +4.9491211545e-01f,
	 +4.7250398175e-01f,
	 +4.5025845187e-01f,
	 +4.2822911724e-01f,
	 +4.0646904846e-01f,
	 +3.8503066744e-01f,
	 +3.6396562111e-01f,
	 +3.4332465702e-01f,
	 +3.2315750106e-01f,
	 +3.0351273767e-01f,
	 +2.8443769281e-01f,
	 +2.6597831993e-01f,
	 +2.4817908928e-01f,
	 +2.3108288077e-01f,
	 +2.1473088065e-01f,
	 +1.9916248234e-01f,
	 +1.8441519145e-01f,
	 +1.7052453552e-01f,
	 +1.5752397834e-01f,
	 +1.4544483940e-01f,
	 +1.3431621840e-01f,
	 +1.2416492516e-01f,
	 +1.1501541504e-01f,
	 +1.0688973002e-01f,
	 +9.9807445563e-02f,
	 +9.3785623531e-02f,
	 +8.8838771015e-02f,
	 +8.4978805416e-02f,
	 +8.2215025731e-02f,
	 +8.0554090146e-02f
};

#endif /* hamming_window128_h */
