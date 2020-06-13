import React, { Component } from "react";
import { axios, getBandFrequencies } from "utils/utils";
import Paper from "@material-ui/core/Paper";
import { withStyles } from "@material-ui/styles";
import Grid from "@material-ui/core/Grid";
import Typography from "@material-ui/core/Typography";
import ListSubheader from "@material-ui/core/ListSubheader";
import ListItem from "@material-ui/core/ListItem";
import ListItemText from "@material-ui/core/ListItemText";
import ListItemSecondaryAction from "@material-ui/core/ListItemSecondaryAction";
import Toolbar from "@material-ui/core/Toolbar";
import TableBody from "@material-ui/core/TableBody";
import InputBase from "@material-ui/core/InputBase";
import Hidden from "@material-ui/core/Hidden";
import List from "@material-ui/core/List";
import Button from "@material-ui/core/Button";
import Radio from "antd/es/radio";
import TextField from "@material-ui/core/TextField";
import Table from "@material-ui/core/Table";
import TableCell from "@material-ui/core/TableCell";
import TableRow from "@material-ui/core/TableRow";
import TableHead from "@material-ui/core/TableHead";
import Input from "@material-ui/core/Input";
import FormControl from "@material-ui/core/FormControl";
import Select from "react-select";
import ProfileSetting from "components/profileSetting";
import _ from "lodash";


const styles = (theme) => ({
    content: {
        padding: theme.spacing(3),
    },
    paramSection: {
        marginTop: 30,
        maxWidth: 1000,
    },
    paramSectionContent: {
        display: "flex",
        flexDirection: "column",
        width: "90%",
        paddingLeft: theme.spacing(2),
        [theme.breakpoints.down("md")]: {
            // maxWidth: 450,
        },
        [theme.breakpoints.up("md")]: {
            alignItems: "center",
        },
    },
    paramControlButtons: {
        width: "80%",
        maxWidth: 600,
        marginTop: 30,
        marginBottom: 30,
    },
    formControl: {
        margin: theme.spacing(1),
        minWidth: 120,
    },
    mobileDoubleText: {
        width: "20vw",
        margin: "3vw",
    },
});

const typeOptions = [
    { value: ["cr", "g65"], label: "CR/G65" },
    { value: ["g50", "g80"], label: "G50/G80" },
    { value: ["kneeLow", "mpo"], label: "Kneelow/Mpo" },
    { value: ["attack", "release"], label: "Attack/Release" },
    { value: ["frepingAlpha"], label: "FrepingAlpha" },
];

// Disable g50 and g80 when control_via = 0
// Disable cr and g65 when control_via = 1
const disabled_row = [
    ["cr", "g65"],
    ["g50", "g80"],
];

const table_background = {
    'Left':"#b0dfe5",
    'Right':"#ffa6a6",
    'Both':"#91f2ad"

}

class Amplification extends Component {
    state = {
        type: typeOptions[0],
        control_via: 1,
        afc: 1,
        // alpha: 1,
        freping: 0,
        global_mpo: 120,
        channel: "Left",
        aligned: 1,
        cr_L: [1, 1, 1, 1, 1, 1],
        cr_R: [1, 1, 1, 1, 1, 1],
        g50_L: [0, 0, 0, 0, 0, 0],
        g50_R: [0, 0, 0, 0, 0, 0],
        g65_L: [0, 0, 0, 0, 0, 0],
        g65_R: [0, 0, 0, 0, 0, 0],
        g80_L: [0, 0, 0, 0, 0, 0],
        g80_R: [0, 0, 0, 0, 0, 0],
        kneeLow_L: [45, 45, 45, 45, 45, 45],
        kneeLow_R: [45, 45, 45, 45, 45, 45],
        mpo_L: [120, 120, 120, 120, 120, 120],
        mpo_R: [120, 120, 120, 120, 120, 120],
        attack_L: [5, 5, 5, 5, 5, 5],
        attack_R: [5, 5, 5, 5, 5, 5],
        release_L: [20, 20, 20, 20, 20, 20],
        release_R: [20, 20, 20, 20, 20, 20],
        frepingAlpha_L: [0, 0, 0, 0, 0, 0],
        frepingAlpha_R: [0, 0, 0, 0, 0, 0],
        bandNumber: 6,
        controlSignalChanged: [0, 0, 0, 0, 0],
        stateBeforeTransmit: {},
    };

    componentDidMount = async () => {
        try {
            await this.loadParameter();
        } catch (error) {
            alert(error);
        }
    };

    fixPrecision = (data) => {
        //console.log(data)
        for (let i; i < data.length; i++) {
            if (data[i] > 1e-4) {
                data[i] = data[i].toFixed(2);
            }
        }
    };

    updateParamTable = (data) => {
        console.log("update param from OSP");
        let param_left = data["left"];
        let param_right = data["right"];
        var g50_Left = param_left["g50"];
        var g80_Left = param_left["g80"];
        var g50_Right = param_right["g50"];
        var g80_Right = param_right["g80"];
        var g65_Left = param_left["g50"];
        var g65_Right = param_right["g50"];

        const bandNumber = g50_Left.length;
        console.log(data);
        for (var i = 0; i < bandNumber; i++) {
            var slope_L = (g80_Left[i] - g50_Left[i]) / 30;
            var slope_R = (g80_Right[i] - g80_Right[i]) / 30;
            var leftVal = Number(g50_Left[i]) + Number(slope_L * 15);
            var rightVal = Number(g50_Right[i]) + Number(slope_R * 15);
            g65_Left[i] = leftVal;
            g65_Right[i] = rightVal;
        }

        this.fixPrecision(param_left["g50"]);
        this.fixPrecision(param_right["g50"]);
        this.fixPrecision(g65_Left);
        this.fixPrecision(g65_Right);
        this.fixPrecision(param_left["g80"]);
        this.fixPrecision(param_right["g80"]);
        this.fixPrecision(param_left["knee_low"]);
        this.fixPrecision(param_right["knee_low"]);
        this.fixPrecision(param_left["mpo_band"]);
        this.fixPrecision(param_right["mpo_band"]);
        this.fixPrecision(param_left["attack"]);
        this.fixPrecision(param_right["attack"]);
        this.fixPrecision(param_left["release"]);
        this.fixPrecision(param_right["release"]);

        let stateData = {
            afc: param_left["afc"],
            freping: param_left["freping"],
            global_mpo: param_left["global_mpo"],
            //TODO: USE LINE ABOVE WHEN ALIGNED IS IN 1/0 FORMAT
            // aligned: bandNumber === 10? param_left["aligned"] : 0,
            aligned: bandNumber === 10 ? (param_left["aligned"] ? 1 : 0) : 0,
            cr_L: Array(bandNumber).fill(1),
            cr_R: Array(bandNumber).fill(1),
            g50_L: param_left["g50"],
            g50_R: param_right["g50"],
            g65_L: g65_Left,
            g65_R: g65_Right,
            g80_L: param_left["g80"],
            g80_R: param_right["g80"],
            kneeLow_L: param_left["knee_low"],
            kneeLow_R: param_right["knee_low"],
            mpo_L: param_left["mpo_band"],
            mpo_R: param_right["mpo_band"],
            attack_L: param_left["attack"],
            attack_R: param_right["attack"],
            release_L: param_left["release"],
            release_R: param_right["release"],
            frepingAlpha_L: param_left["freping_alpha"]
                ? param_left["freping_alpha"]
                : Array(bandNumber).fill(0),
            frepingAlpha_R: param_right["freping_alpha"]
                ? param_right["freping_alpha"]
                : Array(bandNumber).fill(0),
            bandNumber,
        }
        let stateBeforeTransmit =  JSON.parse(JSON.stringify(stateData));
        this.setState({
            stateBeforeTransmit,
            ...stateData
        });

       

    };

    updateParamTableFromDB = (data) => {
        console.log("update from DB")
        let param_left = data["left"];
        let param_right = data["right"];

        let stateData = {
            cr_L: param_left["cr"],
            cr_R: param_right["cr"],
            g50_L: param_left["g50"],
            g50_R: param_right["g50"],
            g65_L: param_left["g65"],
            g65_R: param_right["g65"],
            g80_L: param_left["g80"],
            g80_R: param_right["g80"],
            kneeLow_L: param_left["knee_low"],
            kneeLow_R: param_right["knee_low"],
            mpo_L: param_left["mpo_band"],
            mpo_R: param_right["mpo_band"],
            attack_L: param_left["attack"],
            attack_R: param_right["attack"],
            release_L: param_left["release"],
            release_R: param_right["release"],
            frepingAlpha_L: param_left["freping_alpha"],
            frepingAlpha_R: param_right["freping_alpha"],
            bandNumber: data["bandNumber"],
        }
        let stateBeforeTransmit =  JSON.parse(JSON.stringify(stateData));
        this.setState({
            stateBeforeTransmit,
            ...stateData
        });
        
    };

    loadParameter = async () => {
        try {
            const response = await axios.post("/api/param/getparam");
            let data = response.data;
            console.log(Object.values(data));
            this.updateParamTable(data);
        } catch (error) {
            throw error;
        }
    };

    handleControlViaChange = (event) => {
        this.setState({
            control_via: event.target.value,
        });
    };

    handleAfcChange = (event) => {
        this.setState({
            afc: event.target.value,

        });
    };

    handleFrepingChange = (event) => {
        this.setState({
            freping: event.target.value,
        });
    };

    handleChannelChange = (event) => {
        this.setState({
            channel: event.target.value,
        });
    };

    handleAlignChange = (event) => {
        this.setState({
            aligned: event.target.value,
        });
    };

    handleInputChange = (event) => {
        this.setState({
            [event.target.id]: Number(event.target.value),
        });

    };

    handleTypeChange = (value) => {
        this.setState({
            type: value,
        });
    };

    handleSubBandInputChange = (event) => {
        const { channel } = this.state;
        let inputId = event.target.id.split("_");
        let index = inputId[1];
        let type = inputId[0];
        let type_L = type + "_L";
        let type_R = type + "_R";

        if (channel === "Right") {
            let values_R = this.state[type_R];
            values_R[index] = Number(event.target.value);
            this.setState({
                [type_R]: values_R,
            });
        } else if (channel === "Left") {
            let values_L = this.state[type_L];
            values_L[index] = Number(event.target.value);
            this.setState({
                [type_L]: values_L,
            });
        } else {
            let values_L = this.state[type_L];
            let values_R = this.state[type_R];
            values_L[index] = Number(event.target.value);
            values_R[index] = Number(event.target.value);
            this.setState({
                [type_L]: values_L,
                [type_R]: values_R,
            });
        }

        if (
            type === "cr" ||
            type === "g50" ||
            type === "g80" ||
            type === "g65"
        ) {
            this.updateParameter(index);
        }
    };

    handleAllInputChange = (event) => {

        const { channel, bandNumber } = this.state;
        let inputId = event.target.id.split("_");
        let type = inputId[0];
        let type_L = type + "_L";
        let type_R = type + "_R";

        if (channel === "Right") {
            let values_R = this.state[type_R];
            for (let i = 0; i < bandNumber; i++) {
                values_R[i] = Number(event.target.value);
            }
            this.setState({
                [type_R]: values_R,
            });
        } else if (channel === "Left") {
            let values_L = this.state[type_L];
            for (let i = 0; i < bandNumber; i++) {
                values_L[i] = Number(event.target.value);
            }
            this.setState({
                [type_L]: values_L,
            });
        } else {
            let values_L = this.state[type_L];
            let values_R = this.state[type_R];
            for (let i = 0; i < bandNumber; i++) {
                values_L[i] = Number(event.target.value);
                values_R[i] = Number(event.target.value);
            }

            this.setState({
                [type_L]: values_L,
                [type_R]: values_R,
            });
        }

        if (
            type === "cr" ||
            type === "g50" ||
            type === "g80" ||
            type === "g65"
        ) {
            this.updateAllParameters();
        }
    };

    updateParameter = (index) => {
        const {
            control_via,
            channel,
            cr_L,
            cr_R,
            g65_L,
            g65_R,
            g50_L,
            g50_R,
            g80_L,
            g80_R,
        } = this.state;
        let g50_Left = g50_L.slice();
        let g50_Right = g50_R.slice();
        let g80_Left = g80_L.slice();
        let g80_Right = g80_R.slice();
        let g65_Left = g65_L.slice();
        let g65_Right = g65_R.slice();
        let cr_Left = cr_L.slice();
        let cr_Right = cr_R.slice();

        if (control_via === 1) {
            if (channel === "Both") {
                let cr = cr_Left[index];
                if (cr !== 0) {
                    let slope = (1 - cr) / cr;
                    let g65 = g65_Left[index];
                    let g80 = Number(g65) + Number(slope * 15);
                    let g50 = Number(g65) - Number(slope * 15);
                    g50_Left[index] = g50;
                    g50_Right[index] = g50;
                    g80_Left[index] = g80;
                    g80_Right[index] = g80;
                    this.setState({
                        g50_L: g50_Left,
                        g80_L: g80_Left,
                        g50_R: g50_Right,
                        g80_R: g80_Right,
                    });
                }
            } else if (channel === "Left") {
                let cr = cr_Left[index];
                if (cr !== 0) {
                    let slope = (1 - cr) / cr;
                    let g65 = g65_Left[index];
                    let g80 = Number(g65) + Number(slope * 15);
                    let g50 = Number(g65) - Number(slope * 15);
                    g50_Left[index] = g50;
                    g80_Left[index] = g80;
                    this.setState({
                        g50_L: g50_Left,
                        g80_L: g80_Left,
                    });
                }
            } else {
                let cr = cr_Right[index];
                if (cr !== 0) {
                    let slope = (1 - cr) / cr;
                    let g65 = g65_Right[index];
                    let g80 = Number(g65) + Number(slope * 15);
                    let g50 = Number(g65) - Number(slope * 15);
                    g50_Right[index] = g50;
                    g80_Right[index] = g80;
                    this.setState({
                        g50_R: g50_Right,
                        g80_R: g80_Right,
                    });
                }
            }
        }
    };

    updateAllParameters = () => {
        const {
            control_via,
            channel,
            cr_L,
            cr_R,
            g65_L,
            g65_R,
            g50_L,
            g50_R,
            g80_L,
            g80_R,
            bandNumber,
        } = this.state;
        let g50_Left = g50_L.slice();
        let g50_Right = g50_R.slice();
        let g80_Left = g80_L.slice();
        let g80_Right = g80_R.slice();
        let g65_Left = g65_L.slice();
        let g65_Right = g65_R.slice();
        let cr_Left = cr_L.slice();
        let cr_Right = cr_R.slice();

        if (control_via === 1) {
            if (channel === "Both") {
                for (let i = 0; i < bandNumber; i++) {
                    let cr = cr_Left[i];
                    if (cr !== 0) {
                        let slope = (1 - cr) / cr;
                        let g65 = g65_Left[i];
                        let g80 = Number(g65) + Number(slope * 15);
                        let g50 = Number(g65) - Number(slope * 15);
                        g50_Left[i] = g50;
                        g50_Right[i] = g50;
                        g80_Left[i] = g80;
                        g80_Right[i] = g80;
                    }
                }
                this.setState({
                    g50_L: g50_Left,
                    g80_L: g80_Left,
                    g50_R: g50_Right,
                    g80_R: g80_Right,
                });
            } else if (channel === "Left") {
                for (let i = 0; i < bandNumber; i++) {
                    let cr = cr_Left[i];
                    if (cr !== 0) {
                        let slope = (1 - cr) / cr;
                        let g65 = g65_Left[i];
                        let g80 = Number(g65) + Number(slope * 15);
                        let g50 = Number(g65) - Number(slope * 15);
                        g50_Left[i] = g50;
                        g80_Left[i] = g80;
                    }
                }
                this.setState({
                    g50_L: g50_Left,
                    g80_L: g80_Left,
                });
            } else {
                for (var i = 0; i < bandNumber; i++) {
                    let cr = cr_Right[i];
                    if (cr !== 0) {
                        let slope = (1 - cr) / cr;
                        let g65 = g65_Right[i];
                        let g80 = Number(g65) + Number(slope * 15);
                        let g50 = Number(g65) - Number(slope * 15);
                        g50_Right[i] = g50;
                        g80_Right[i] = g80;
                    }
                }
                this.setState({
                    g50_R: g50_Right,
                    g80_R: g80_Right,
                });
            }
        }
    };

    generateParams = () => {
        const {
            afc,
            aligned,
            global_mpo,
            g50_L,
            g50_R,
            g80_L,
            g80_R,
            kneeLow_L,
            kneeLow_R,
            mpo_L,
            mpo_R,
            attack_L,
            attack_R,
            release_L,
            release_R,
            frepingAlpha_L,
            frepingAlpha_R,
            freping,
            bandNumber,
        } = this.state;

        var params = {
            left: {
                en_ha: 1,
                afc: afc,
                freping: freping,
                rear_mics: 0,
                g50: g50_L,
                g80: g80_L,
                knee_low: kneeLow_L,
                mpo_band: mpo_L,
                attack: attack_L,
                release: release_L,
                global_mpo: global_mpo,
                freping_alpha: frepingAlpha_L,
            },
            right: {
                en_ha: 1,
                afc: afc,
                freping: freping,
                rear_mics: 0,
                g50: g50_R,
                g80: g80_R,
                knee_low: kneeLow_R,
                mpo_band: mpo_R,
                attack: attack_R,
                release: release_R,
                global_mpo: global_mpo,
                freping_alpha: frepingAlpha_R,
            },
        };
        if (bandNumber === 10) {
            console.log("10 band param with aligned: ", aligned);
            params = {
                left: {
                    ...params.left,
                    aligned,
                },
                right: {
                    ...params.right,
                    aligned,
                },
            };
        }
        return params;
    };

    generateParamsProfile = () => {
        const {
            afc,
            global_mpo,
            cr_L,
            cr_R,
            g50_L,
            g50_R,
            g80_L,
            g80_R,
            g65_L,
            g65_R,
            kneeLow_L,
            kneeLow_R,
            mpo_L,
            mpo_R,
            attack_L,
            attack_R,
            release_L,
            release_R,
            frepingAlpha_L,
            frepingAlpha_R,
            freping,
        } = this.state;

        var params = {
            left: {
                afc: afc,
                freping: freping,
                cr: cr_L,
                g50: g50_L,
                g65: g65_L,
                g80: g80_L,
                knee_low: kneeLow_L,
                mpo_band: mpo_L,
                attack: attack_L,
                release: release_L,
                global_mpo: global_mpo,
                freping_alpha: frepingAlpha_L,
            },
            right: {
                afc: afc,
                freping: freping,
                cr: cr_R,
                g50: g50_R,
                g65: g65_R,
                g80: g80_R,
                knee_low: kneeLow_R,
                mpo_band: mpo_R,
                attack: attack_R,
                release: release_R,
                global_mpo: global_mpo,
                freping_alpha: frepingAlpha_R,
            },
        };

        return params;
    };

    generateAlignControl = (bandNumber) => {
        if (bandNumber === 10) {
            return (
                <ListItem>
                    <ListItemText primary="Aligned" />
                    <ListItemSecondaryAction>
                        <Radio.Group
                            value={this.state.aligned}
                            buttonStyle="solid"
                            onChange={this.handleAlignChange}
                        >
                            <Radio.Button value={1}>On</Radio.Button>
                            <Radio.Button value={0}>Off</Radio.Button>
                        </Radio.Group>
                    </ListItemSecondaryAction>
                </ListItem>
            );
        }
    };
    handleSaveParamsToDB = async (profileName) => {
        var params = JSON.stringify(this.generateParamsProfile());
        try {
            const response = await axios.post("/api/param/amplification", {
                profileName: profileName,
                bandNumber: this.state.bandNumber,
                parameters: params,
            });
            console.log(response.data);
        } catch (error) {
            alert("This profile name has already existed");
        }
    };

    handleLoadParamsFromDB = async (profile) => {
        try {
            const params = await JSON.parse(profile);
            this.updateParamTableFromDB(params);
        } catch (error) {
            alert("Error parsing JSON");
        }
    };

    onTransmitParams = async () => {
        var params = this.generateParams();
        try {
            const response = await axios.post("/api/param/setparam", {
                method: "set",
                data: params,
            });
            const data = response.data;
            // Update the current transmitted state
            this.setState(prevState => {
                //omit old stateBeforeTransmit
                let { stateBeforeTransmit, ...stateData} = prevState;
                let newStateBeforeTransmit =  JSON.parse(JSON.stringify(stateData));;
                return {stateBeforeTransmit: newStateBeforeTransmit}
            })
            console.log(data);
        } catch (error) {
            alert(error.response.data);
        }
    };

    paramTableRowMobile = (subBand, index) => {
        const { classes } = this.props;
        const { channel, type, bandNumber } = this.state;

        if (index !== bandNumber) {
            return (
                <ListItem key={`subBand_${subBand}_index_${index}`}>
                    <ListItemText primary={subBand} />
                    <ListItemSecondaryAction>
                        {type.value.map((data_id) => {
                            var curr_data_id = data_id;
                            if (channel === "Right") {
                                curr_data_id += "_R";
                            } else {
                                curr_data_id += "_L";
                            }
                            return (
                                <TextField
                                    className={
                                        type.value.length === 2
                                            ? classes.mobileDoubleText
                                            : null
                                    }
                                    key={data_id + "_" + index}
                                    id={data_id + "_" + index}
                                    type="number"
                                    margin="dense"
                                    variant="outlined"
                                    value={this.state[curr_data_id][index]}
                                    onChange={this.handleSubBandInputChange}
                                />
                            );
                        })}
                    </ListItemSecondaryAction>
                </ListItem>
            );
        } else {
            return (
                <ListItem key={`subBand_${subBand}_index_${index}`}>
                    <ListItemText primary={subBand} />
                    <ListItemSecondaryAction>
                        {type.value.map((data_id) => {
                            return (
                                <TextField
                                    className={
                                        type.value.length === 2
                                            ? classes.mobileDoubleText
                                            : null
                                    }
                                    key={data_id + "_all"}
                                    id={data_id + "_all"}
                                    type="number"
                                    margin="dense"
                                    variant="outlined"
                                    onChange={this.handleAllInputChange}
                                />
                            );
                        })}
                    </ListItemSecondaryAction>
                </ListItem>
            );
        }
    };

    // Generate table row for each parameter
    // Disable cr and g65 row when control via = g50/g80
    // Disable g58 and g80 row when control via = cr/g65
    paramTableRow = (type) => {
        const { channel, control_via } = this.state;
        var data_id = type;
        if (channel === "Right") {
            data_id += "_R";
        } else {
            data_id += "_L";
        }

        var disabled_style = disabled_row[control_via].includes(type)
            ? { backgroundColor: "#bdbdbd", testColor: "gray" }
            : {};

        return (
            <TableRow hover key={type} style={disabled_style}>
                <TableCell>
                    <Typography variant="subtitle1" style={this.getParamLabelColor(data_id)}>
                        {type}
                    </Typography>
                </TableCell>
                {this.state[data_id].map((value, index) => (
                    <TableCell
                        key={`row_index_${index}`}
                        children={
                            <FormControl>
                                <InputBase
                                    disabled={disabled_row[
                                        control_via
                                    ].includes(type)}
                                    id={type + "_" + index}
                                    fullWidth
                                    type="number"
                                    value={value}
                                    onChange={this.handleSubBandInputChange}
                                />
                            </FormControl>
                        }
                    />
                ))}
                <TableCell
                    children={
                        <FormControl>
                            <Input
                                id={type + "_all"}
                                fullWidth
                                type="number"
                                disabled={disabled_row[control_via].includes(
                                    type
                                )}
                                onChange={this.handleAllInputChange}
                            />
                        </FormControl>
                    }
                />
            </TableRow>
        );
    };

    renderControlSignals = () => {
        return (
            <List
                subheader={
                    <ListSubheader>Researcher Page Controls</ListSubheader>
                }
            >
                <ListItem>
                    <ListItemText primary="Control Via" />
                    <ListItemSecondaryAction>
                        <Radio.Group
                            value={this.state.control_via}
                            buttonStyle="solid"
                            onChange={this.handleControlViaChange}
                        >
                            <Radio.Button value={0}>G50/G80</Radio.Button>
                            <Radio.Button value={1}>CR/G65</Radio.Button>
                        </Radio.Group>
                    </ListItemSecondaryAction>
                </ListItem>
                <ListItem>
                    <ListItemText primary="Channel" />
                    <ListItemSecondaryAction>
                        <Radio.Group
                            value={this.state.channel}
                            buttonStyle="solid"
                            onChange={this.handleChannelChange}
                        >
                            <Radio.Button value="Left">Left</Radio.Button>
                            <Radio.Button value="Right">Right</Radio.Button>
                            <Radio.Button value="Both">Both</Radio.Button>
                        </Radio.Group>
                    </ListItemSecondaryAction>
                </ListItem>
            </List>
        );
    };

    // Get the control signal label color by comparing current value and previously transmitted value
    // If value is changed, highlight label colors
    getControlLabelColor = (label) => {
        return this.state[label] !== this.state.stateBeforeTransmit[label]?{ color: "#F50057" }: {color:'black'}
    }

    // Get the parameter label color by comparing current value and previously transmitted value
    // If value is changed, highlight label colors
    getParamLabelColor = (label) => {
        console.log(label, this.state[label])
        console.log(label, this.state.stateBeforeTransmit[label])
        return _.isEqual(this.state[label],this.state.stateBeforeTransmit[label]) ?{color:'black'}:{ color: "#F50057" };
    }
    renderParamControls = () => {
        const { bandNumber } = this.state;
        return (
            <List subheader={<ListSubheader>RTMHA Controls</ListSubheader>}>
                <ListItem>
                    <ListItemText primary="AFC" 
                    style={this.getControlLabelColor('afc')}
                    />

                    <ListItemSecondaryAction>
                        <Radio.Group
                            value={this.state.afc}
                            buttonStyle="solid"
                            onChange={this.handleAfcChange}
                        >
                            <Radio.Button value={1}>On</Radio.Button>
                            <Radio.Button value={0}>Off</Radio.Button>
                        </Radio.Group>
                    </ListItemSecondaryAction>
                </ListItem>
                <ListItem>
                    <ListItemText primary="Freping" 
                    style={this.getControlLabelColor('freping')}
                    />
                    <ListItemSecondaryAction>
                        <Radio.Group
                            value={this.state.freping}
                            buttonStyle="solid"
                            onChange={this.handleFrepingChange}
                        >
                            <Radio.Button value={1}>On</Radio.Button>
                            <Radio.Button value={0}>Off</Radio.Button>
                        </Radio.Group>
                    </ListItemSecondaryAction>
                </ListItem>
                <ListItem>
                    <ListItemText primary="Global MPO" 
                    style={this.getControlLabelColor('global_mpo')}
                    />
                    <ListItemSecondaryAction>
                        <TextField
                            id="global_mpo"
                            type="number"
                            margin="dense"
                            variant="outlined"
                            value={this.state.global_mpo}
                            onChange={this.handleInputChange}
                        />
                    </ListItemSecondaryAction>
                </ListItem>
                {
                    // Render Aligned control based on the bandNUmber
                    bandNumber === 10 && (
                        <ListItem>
                            <ListItemText primary="Aligned" />
                            <ListItemSecondaryAction>
                                <Radio.Group
                                    value={this.state.aligned}
                                    buttonStyle="solid"
                                    onChange={this.handleAlignChange}
                                >
                                    <Radio.Button value={1}>On</Radio.Button>
                                    <Radio.Button value={0}>Off</Radio.Button>
                                </Radio.Group>
                            </ListItemSecondaryAction>
                        </ListItem>
                    )
                }
            </List>
        );
    };
    render() {
        const { classes } = this.props;
        const { type, bandNumber, channel} = this.state;
        return (
            <div className={classes.content}>
                <Grid container spacing={3}>
                    <Grid item md={6} xs={12} spacing={10}>
                        <Paper style={{ maxWidth: 500 }}>
                            {this.renderControlSignals()}
                        </Paper>
                        <Paper style={{ maxWidth: 500, marginTop: 30 }}>
                            {this.renderParamControls()}
                        </Paper>
                    </Grid>
                    <Grid item md={6} xs={12}>
                        <ProfileSetting
                            save={this.handleSaveParamsToDB}
                            load={this.handleLoadParamsFromDB}
                            currProfileBand={bandNumber}
                        />
                    </Grid>
                </Grid>

                <Paper className={classes.paramSection} style={{backgroundColor:table_background[channel]}}>
                    <Toolbar>
                        <Typography color="textSecondary" gutterBottom>
                            Parameter Settings
                        </Typography>
                    </Toolbar>
                    <Hidden mdUp>
                        <div className={classes.paramSectionContent}>
                            <Select
                                defaultValue={typeOptions[0]}
                                value={type}
                                onChange={this.handleTypeChange}
                                options={typeOptions}
                            />
                            <List>
                                {getBandFrequencies(
                                    bandNumber
                                ).map((value, index) =>
                                    this.paramTableRowMobile(value, index)
                                )}
                            </List>
                            <Grid
                                container
                                spacing={2}
                                className={classes.paramControlButtons}
                            >
                                <Grid item xs={12} sm={6}>
                                    <Button
                                        variant="contained"
                                        color="secondary"
                                        fullWidth
                                        onClick={this.loadParameter}
                                    >
                                        Pull
                                    </Button>
                                </Grid>
                                <Grid item xs={12} sm={6}>
                                    <Button
                                        variant="contained"
                                        color="secondary"
                                        fullWidth
                                        onClick={this.onTransmitParams}
                                    >
                                        Transmit
                                    </Button>
                                </Grid>
                            </Grid>
                        </div>
                    </Hidden>

                    <Hidden smDown>
                        <div className={classes.paramSectionContent}>
                            <Table >
                                <TableHead>
                                    <TableRow>
                                        {[""]
                                            .concat(
                                                getBandFrequencies(bandNumber)
                                            )
                                            .map((text) => (
                                                <TableCell key={text}>
                                                    <Typography
                                                        variant="subtitle1"
                                                        color="textSecondary"
                                                    >
                                                        {text}
                                                    </Typography>
                                                </TableCell>
                                            ))}
                                    </TableRow>
                                </TableHead>
                                <TableBody>
                                    {[
                                        "cr",
                                        "g50",
                                        "g65",
                                        "g80",
                                        "kneeLow",
                                        "mpo",
                                        "attack",
                                        "release",
                                        "frepingAlpha",
                                    ].map((text) => this.paramTableRow(text))}
                                </TableBody>
                            </Table>
                            <Grid
                                container
                                spacing={2}
                                className={classes.paramControlButtons}
                            >
                                <Grid item xs={12} sm={6}>
                                    <Button
                                        variant="contained"
                                        color="secondary"
                                        fullWidth
                                        onClick={this.loadParameter}
                                    >
                                        Pull
                                    </Button>
                                </Grid>
                                <Grid item xs={12} sm={6}>
                                    <Button
                                        variant="contained"
                                        color="secondary"
                                        fullWidth
                                        onClick={this.onTransmitParams}
                                    >
                                        Transmit
                                    </Button>
                                </Grid>
                            </Grid>
                        </div>
                    </Hidden>
                </Paper>
            </div>
        );
    }
}

export default withStyles(styles)(Amplification);
