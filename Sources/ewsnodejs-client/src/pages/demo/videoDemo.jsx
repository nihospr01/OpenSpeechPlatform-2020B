import React from 'react';
import { withStyles } from '@material-ui/styles';
import VideoPlayer from 'components/VideoPlayer';
import AudioPlayer from 'components/AudioPlayer';


function VideoDemo(): React.Component {
    return (
        <div>
            <VideoPlayer 
                videoName = "mov_bbb.mp4"
                hasUrl = {true} 
                videoSrc = "http://www.w3schools.com/html/mov_bbb.mp4"
                width = "50%"/>
            <VideoPlayer 
                videoName = "lips_demo.mp4"
                hasUrl = {false} 
                width = "80%"/>
            <VideoPlayer 
                videoName = "lips_demoo.mp4"
                hasUrl = {false} 
                width = "80%"/>
            <AudioPlayer
                audioName = "bed.wav"
            />
            <AudioPlayer
                audioName = "bedd.wav"
            />
            
        </div>
    )
}

export default VideoDemo;