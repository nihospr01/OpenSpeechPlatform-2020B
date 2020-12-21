import React from 'react';

import { withRouter } from 'react-router-dom';
import { compose } from 'recompose';
import withUserAuth from 'context/withUserAuth';
import { axios } from 'utils/utils';
import Grid from '@material-ui/core/Grid';

class Timer extends React.Component {
    constructor(props) {
      super(props);
      this.state = { seconds: 0 };
    }

    getTime = async() => {
        try {
            const respnse = await axios.post("/api/param/getTime")
            const data = respnse.data
            //console.log(data)
            this.setState(state => ({
                seconds: Object.values(data)[0]
            }));
        }
        catch(error) {
            alert(error.response.data);
        }
    }

    getCurrentWord = (data) =>{
        console.log(this.state.seconds)
        let time = this.state.seconds % data.length;
        let words = data.punct.split(' ');
        let cnt = 0;
        while (time > data.words[cnt].end){
            //console.log(data.words[cnt].end);
            cnt++;
        }
        let prevWords = words.slice(0, cnt).join(' ');
        let currWord = " " +words[cnt];
        let restWords = " " + words.slice(cnt+1).join(' ');
        return [prevWords, currWord, restWords]
    }

    componentDidMount() {
      this.interval = setInterval(() => this.getTime(), 400);
    }
  
    componentWillUnmount() {
      clearInterval(this.interval);
    }
  
    render() {
        const {
            filename,
        } = this.props

        const [prev, curr, rest] = this.getCurrentWord(filename)

        return (
            // <div>
            // Seconds: {this.state.seconds}
            // {filename}
            // </div>
            <Grid item xs={12}>
            <p>
                {prev}
                <font color="red">
                    {curr}
                </font>
                {rest}
            </p>
            </Grid>
        );
    }
  }
  
  export default compose(
    withUserAuth,
    withRouter,
)(Timer);