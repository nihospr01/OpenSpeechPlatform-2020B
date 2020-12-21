import React, {useState, useEffect} from 'react';
import ReactAudioPlayer from 'react-h5-audio-player';
import { withRouter } from 'react-router-dom';
import { compose } from 'recompose';
import axios from 'axios';
import withUserAuth from 'context/withUserAuth';
import 'react-h5-audio-player/lib/styles.css';
import Alert from '@material-ui/lab/Alert';

type Props = {
    audioName: string,
    headerPrefix: string,
};

const serverPrefix = "/api/researcher/audio?name="


function AudioPlayer({audioName}:Props): React.Component {

    const [reqError, setReqError] = useState(null)

    const url = serverPrefix + audioName

    useEffect(() => {
        axios.get(url).catch(error => {
            if (error?.response?.status != null) {
                if(error.response.status == 404) {
                    setReqError("Can't find audio: " + audioName)
                }
                else {
                    setReqError("Can't play audio: " + audioName)
                }

            }
            
        })
    },[url, setReqError, audioName])

    return (
        <div>
            <ReactAudioPlayer
                autoPlay = {false}
                src = {reqError == null ? url : null}
                showJumpControls = {false}
            /> 
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
)(AudioPlayer);