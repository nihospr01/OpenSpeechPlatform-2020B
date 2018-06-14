#include "speech_enhancement.h"
#include <math.h>
#include <stdlib.h>
#include "array_utilities.h"
#include <stdio.h>
void speech_enhancement(float *input,
          int ntype,
          int stype,
          float sparam,
          size_t len,
          int fsamp,
          float *out)
{
    float att;
    float rel;
	
    float aLP;
    float rLP;
    float npeak;
    float tau;
    float aLPv;
    float rLPv;
    float inc;
    double dec;
    float lin_inc;
    float lin_dec;
    double a1;
    float a2;
    double aHE;
    double bHE;
    float aCB;
    float bCB;
    float xpow[len];
    float npow[len];
    float vpow[len];
    float xabs;
    float peak[len];
    float valley[len];
	
	  float delta;
	  int n;
	
    float n1;
    float prob;
    float p;
	  float b;
	  float gain[len];
	
	
    /* Attack time in msec */
    att = 3;

    /* Attack filter coefficient */
    aLP = exp(-1.0 / (0.001* att * fsamp));
    
    /* Release time in msec */
    rel = 50;
    
    /* Release filter coefficient */
    rLP = exp(-1.0 / (0.001 * rel * fsamp));

    /* Time in ms to average the signal to prime the noise estimation */
    npeak = round(0.05 * fsamp);
    
    /* Parameters for the valley detection */
    
    /* Attack filter coefficient */
    aLPv = exp(-1.0 / (0.01 * fsamp));
    
    /* Release filter coefficient */
    rLPv = exp(-1.0 / (0.1 * fsamp));
    
    /* Parameters and storage for the Arslan et al. power averaging */
    /* Maximum increase in dB/sec */
    inc = 10;
    
    /* Maximum decrease in dB/sec */
    dec = -25;
    
    lin_inc = pow(10,inc/20);
    lin_dec = pow(10,dec/20);
    
    a1 = pow(lin_inc,1/fsamp);
    a2 = pow(lin_dec,1/fsamp);
    
    /* Parameters for the Hirsch and Ehrlicher weighted noise averaging */
    
    tau = 100;
    /* LP filter coefficient */
    aHE = exp(-1.0 / (0.001 * tau * fsamp));
    
    /* Speech/noise threshold */
    bHE = 2;
    
    /* Parameters for the Cohen and Berdugo MCRA */
    
    delta=10; //Threshold for P[speech]
    
    tau=10; //Time constant in msec
    aCB=exp(-1/(0.001*tau*fsamp)); //LP filter coefficient for ave prob
    
    tau=200; //Time constant in msec
    bCB=exp(-1/(0.001*tau*fsamp)); //LP filter coefficient for noise ave
    
    /* Peak detect and valley detect the envelope of the noisy speech */
    
    peak[0] = array_mean(input,len);
    valley[0] = peak[0];
    
    for(n = 1; n<len; n++)
    {
        xabs = fabsf(input[n]);
        /* Peak detect */
        if (xabs >= peak[n-1])
            peak[n]=aLP*peak[n-1] + (1-aLP)*xabs;
        else
            peak[n]=rLP*peak[n-1];
        
        /* Valley detect */
        
        if (xabs <= valley[n-1])
            valley[n]=aLPv*valley[n-1] + (1-aLPv)*xabs;
        else
            valley[n]=valley[n-1]/rLPv;
    }
    
    array_square(peak, xpow, len);
	
    /* Noise power estimation */
    
    npow[0] = xpow[0];
    array_square(valley,vpow, len);
    
    if (ntype == 1)
    {
        /* Power estimation using limits on change (ref: Arslan et al.) */
        for(n=1;n<len;n++)
        {
            n1 = (xpow[n]<a1*npow[n-1])?xpow[n]:a1*npow[n-1];
            npow[n] = (n1>a2*npow[n-1])?n1:a2*npow[n-1];
        }
    
    }
    else if (ntype == 2)
    {
        /* Noise power estimation using the weighted averaging of Hirsch and Ehrlicher */
        for(n=1;n<len;n++)
        {
            /* Update noise average if new signal sample is close to the noise */
            if (xpow[n] < bHE*npow[n-1])
                npow[n] = aHE*npow[n-1] + (1-aHE)*xpow[n];
            /* Otherwise keep the previous noise estimate */
            else
                npow[n] = npow[n-1];
            
        }
    }
    else if (ntype == 3)
    {
        /* Noise power estimation using MCRA of Cohen and Berdugo */
        prob = 0.5; // Prob of first sample being speech
        for (n=1; n< len;n++)
        {
            // P[speech] for this sample
            if (xpow[n]>delta*vpow[n])
                p=1;
            else
                p=0;
            prob = aCB*prob + (1-aCB)*p; //Smooth P[speech]
            
            b = bCB + (1-bCB)*prob; //Convert P[speech] into time constant
            
            npow[n]= b*npow[n-1] + (1-b)*xpow[n]; //Average the noise
					  //printf("%f\n",npow[n]);
        }
    }
    
    /* Spectral subtraction gain (linear scale) */
    if (stype ==1 )
        /* oversubtraction */
    {
			for(n=0;n<len;n++)
			{
				gain[n] = (xpow[n] - npow[n]*sparam + 0.000001)/(xpow[n] + 0.000001);
				if (gain[n]<=0)
					gain[n] = 1E-30;
				out[n] = gain[n]*input[n];
			}
    }
    else if(stype == 0)
    {
			for(n=0;n<len;n++)
        out[n] = input[n];

		}
	else
	{
		printf("Wrong input for Spectral subtraction\n");
	}
	
}
