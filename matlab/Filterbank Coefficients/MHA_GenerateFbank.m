function [filtCoeffs,cfreq]=MHA_GenerateFbank(nbands,fsamp)

% Check the sampling rate
if fsamp < 12000
    fprintf('mhafb_fbank: Sampling rate too low, outputs set to zero\n');
    return;
end

% Set the filter size and transition band width
tfir=6; %Length of the FIR filter impulse response in msec
% ft=175; %Half the width of the filter transition region for 6 bands (175-Kates) (130-Boothroyd)
ft=130; %Half the width of the filter transition region for 6 bands (175-Kates) (130-Boothroyd)

% Select the band edges and center frequencies
% cfreq=[.250, .500, 1, 2, 4, 6]*1000; %Audiometric center frequencies
cfreq=[177, 354, 707, 1414, 2828, 5667]; % Boothroyd cfreq
% cfreq=[200, 700, 1375, 2775, 6025];

edge=0.5*(cfreq(1:nbands-1) + cfreq(2:nbands)); %Band edges

% Initialize the filter parameters
fnyq=0.5*fsamp; %Nyquist frequency
nfir=round(0.001*tfir*fsamp); %Length of the FIR filters in samples
nfir=2*floor(nfir/2); %Force filter length to be even

% First band is a lowpass filter
gain=[1 1 0 0];
f=[0,(edge(1)-ft),(edge(1)+ft),fnyq];
b=fir2(nfir,f/fnyq,gain,hann(nfir+1)); %FIR filter design
filtCoeffs(1,:)=b;
% grpdelay(b, 1);

% figure;
% freqz(b,1);
% hold on
[h(:,1) w(:,1)] = freqz(b,1,'whole',fsamp);

% Last band is a highpass filter
gain=[0 0 1 1];
f=[0,(edge(nbands-1)-ft),(edge(nbands-1)+ft),fnyq];
b=fir2(nfir,f/fnyq,gain,hann(nfir+1)); %FIR filter design
filtCoeffs(nbands,:)=b;
% grpdelay(b, 1);

% freqz(b,1);
[h(:,nbands) w(:,nbands)] = freqz(b,1,'whole',fsamp);


% Remaining bands are bandpass filters
if nbands > 2
    gain=[0 0 1 1 0 0];
    for n=2:nbands-1
        f=[0,(edge(n-1)-ft),(edge(n-1)+ft),(edge(n)-ft),(edge(n)+ft),fnyq];
        b=fir2(nfir,f/fnyq,gain,hann(nfir+1)); %FIR filter design
        filtCoeffs(n,:)=b;
%         grpdelay(b, 1);
%         freqz(b,1);
        [h(:,n) w(:,n)] = freqz(b,1,'whole',fsamp);
    end
end

% figure;
% plot(mag2db(fftshift(abs(sum(h(1:length(h)/2, :),2)))));
% hold on
% figure;
% plot(unwrap(fftshift(angle(sum(h(1:length(h)/2, :),2)))));

end