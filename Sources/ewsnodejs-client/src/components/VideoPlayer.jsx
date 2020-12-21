import React, {useMemo, useEffect, useState} from 'react';
import Alert from '@material-ui/lab/Alert';
import { withRouter } from 'react-router-dom';
import { compose } from 'recompose';
import withUserAuth from 'context/withUserAuth';
import axios from 'axios';

type Props = {
    videoSrc: ?string,
    hasUrl: boolean,
    videoName: string,
    width: ?string,
    loop: ?string,
    autoPlay: ?boolean,
};


const serverPrefix = "/api/researcher/video?name="
const type = "video/mp4"

function VideoPlayer({videoSrc, 
                      hasUrl, 
                      videoName, 
                      width, 
                      loop, 
                      autoPlay}:Props): React.Component {

    const url = useMemo(
        () => hasUrl ? videoSrc : serverPrefix + videoName,
     [hasUrl, videoSrc, serverPrefix]);

     const [reqError, setReqError] = useState(null)

     useEffect(() => {
        if (!hasUrl) {
            axios.get(url).catch(error => {
                if (error?.response?.status != null) {
                    if(error.response.status == 404) {
                        setReqError("Can't find video: " + videoName)
                    }
                    else {
                        setReqError("Can't play video: " + videoName)
                    }
                }
                
            })
        }
        
    },[url, setReqError, videoName])

    
    return (
        <div>
            <video 
                controls = {true}
                autoPlay = {autoPlay}
                preload = "auto"
                width = {width}
                loop ={loop}
                playsInline = {true}> 
                <source 
                    src = {url}
                    type = {type}>
                </source> 
            </video>
            {reqError && 
                <Alert severity="warning">
                    {reqError}
                </Alert>}
        </div>
    );

  }
  
  export default compose(
    withUserAuth,
    withRouter,
)(VideoPlayer);