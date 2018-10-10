li = dir(fullfile('*.wav'));
l = length(li);
for i = 1:l
    [y,fs] = audioread(li(i).name);
    y = resample(y,3,1);
    y = [y,y];
    audiowrite(li(i).name,y,96000);
end
