import React, { Component } from 'react';
import Button from '@material-ui/core/Button';
import Grid from '@material-ui/core/Grid';
import {axios} from 'utils/utils';


class Reset extends Component {
    state = {
        complete: false
    }

    reset = async() => {
        const dataInput ={
            left:{
                alpha: 0.0,
                en_ha:1,
                afc:1,
                freping:1,
                rear_mics:0,
                g50:[0,0,0,0,0,0],
                g80:[0,0,0,0,0,0],
                knee_low:[45,45,45,45,45,45],
                mpo_band:[120,120,120,120,120,120],
                attack:[5,5,5,5,5,5],
                release:[20,20,20,20,20,20],
                global_mpo:120,
                freping_alpha:[0,0,0,0,0.019999999552965164,0]
            },
            right:{
                en_ha:1,
                afc:1,
                freping:1,
                rear_mics:0,
                g50:[0,0,0,0,0,0],
                g80:[0,0,0,0,0,0],
                knee_low:[45,45,45,45,45,45],
                mpo_band:[120,120,120,120,120,120],
                attack:[5,5,5,5,5,5],
                release:[20,20,20,20,20,20],
                global_mpo:120,
                freping_alpha:[0,0,0,0,0.019999999552965164,0]
            }
        };
        try {
            const response = await axios.post("/api/param/setparam", {
                method: 'set',
                data: dataInput,
            });
            const data = response.data;
            console.log(data);
            this.setState({complete: true});
        }
        catch(error) {
            alert(error.response.data);
        }
    }

    render() {
        var buttonStyle = {
            display: 'block',
            width: '75%',
            minHeight: '100px',
            fontSize: '30px',
            justifyContent: 'center',
            textTransform: 'none',
        };
        const {complete} = this.state;
        return (
            <div>
                <Grid container justify="center">
                    <Button variant='outlined' color='primary' style={buttonStyle} variant='contained' onClick={this.reset}>
                        Reset to Original Prescription
                    </Button>
                    { complete ? 
                        <Button style={buttonStyle}>
                        Successfully Reset
                        </Button>
                     : <Button />}  
                </Grid>

            </div>
        )
    }
}

export default Reset;