import React, { Component } from "react";
import { withStyles } from "@material-ui/styles";
import Stepper from "@material-ui/core/Stepper";
import Step from "@material-ui/core/Step";
import StepLabel from "@material-ui/core/StepLabel";
import StepContent from "@material-ui/core/StepContent";
import Button from "@material-ui/core/Button";
import Typography from "@material-ui/core/Typography";
import Grid from "@material-ui/core/Grid";
import FormControl from "@material-ui/core/FormControl";
import InputLabel from "@material-ui/core/InputLabel";
import FormHelperText from "@material-ui/core/FormHelperText";
import Select from "@material-ui/core/Select";
import FormGroup from "@material-ui/core/FormGroup";
import FormControlLabel from "@material-ui/core/FormControlLabel";
import Checkbox from "@material-ui/core/Checkbox";
import TextField from "@material-ui/core/TextField";
import Paper from "@material-ui/core/Paper";
import {  axios } from "utils/utils";
import { Link, withRouter } from "react-router-dom";
import { compose } from "recompose";
import withUserAuth from "context/withUserAuth";

const styles = (theme) => ({
  formControl: {
    margin: theme.spacing(0),
    minWidth: 120,
  },
  illnessCheckBox: {
    height: 10,
  },
  relationCheckBox: {
    minWidth: 150,
  },
  inputLabel: {
    width: 320,
  },
  fullSelect: {
    minWidth: 280,
    marginBottom: 8,
  },
});

const getEarConditions = () => {
  return ["No Hearing", "Limited Hearing", "Normal Hearing"];
};

const getSteps = () => {
  return [
    "Medical History",
    "Devemopment History",
    "Education History",
    "Family History",
  ];
};

const getIllnesses = () => {
  return ["A", "B", "C", "D", "E", "F", "G"];
};

const getRelations = () => {
  return ["Parent", "Child", "Sibling", "Grandparent", "Grandchild", "Other"];
};

class SurveyPage extends Component {
  state = {
    activeStep: 0,
    leftEar: "",
    rightEar: "",
    development: "",
    education: "",
    personalIllnesses: [],
    siblingIllnesses: [],
    personalIllnessesInfo: {},
    siblingIllnessesInfo: {},
  };

  handleNext = () => {
    this.setState({ activeStep: this.state.activeStep + 1 });
  };

  handleBack = () => {
    this.setState({ activeStep: this.state.activeStep - 1 });
  };

  handleChangeEarInfo = (name) => (event) => {
    this.setState({
      [name]: event.target.value ? parseInt(event.target.value) : "",
    });
  };

  handleChangeUserInfo = (name) => (event) => {
    this.setState({ [name]: event.target.value ? event.target.value : "" });
  };

  handleChangeIllness = (name, sibling) => (event) => {
    if (sibling === false) {
      const { personalIllnesses, personalIllnessesInfo } = this.state;
      if (event.target.checked) {
        personalIllnesses.push(name);
        personalIllnesses.sort();
        personalIllnessesInfo[name] = ["", "", ""];
        this.setState({ personalIllnesses, personalIllnessesInfo });
      } else {
        delete personalIllnessesInfo[name];
        this.setState({
          personalIllnesses: personalIllnesses.filter(
            (illness) => name !== illness
          ),
          personalIllnessesInfo,
        });
      }
    } else {
      const { siblingIllnesses, siblingIllnessesInfo } = this.state;
      if (event.target.checked) {
        siblingIllnesses.push(name);
        siblingIllnesses.sort();
        siblingIllnessesInfo[name] = [[], "", "", ""];
        this.setState({ siblingIllnesses, siblingIllnessesInfo });
      } else {
        delete siblingIllnessesInfo[name];
        this.setState({
          siblingIllnesses: siblingIllnesses.filter(
            (illness) => name !== illness
          ),
          siblingIllnessesInfo,
        });
      }
    }
  };

  handleChangeIllnessInfo = (name, sibling, index) => (event) => {
    if (sibling === false) {
      const { personalIllnessesInfo } = this.state;
      personalIllnessesInfo[name][index] = event.target.value;
      this.setState({ personalIllnessesInfo });
    } else {
      const { siblingIllnessesInfo } = this.state;
      if (index === 0) {
        siblingIllnessesInfo[name][0].includes(event.target.value)
          ? (siblingIllnessesInfo[name][0] = siblingIllnessesInfo[
              name
            ][0].filter((relation) => event.target.value !== relation))
          : siblingIllnessesInfo[name][0].push(event.target.value);
      } else {
        siblingIllnessesInfo[name][index] = event.target.value;
      }
      this.setState({ siblingIllnessesInfo });
    }
  };

  parseLog = () => {
    const {
      leftEar,
      rightEar,
      personalIllnesses,
      personalIllnessesInfo,
      development,
      education,
      siblingIllnesses,
      siblingIllnessesInfo,
    } = this.state;

    let illnesses = {},
      illnessesFam = {};
    for (let illness of personalIllnesses) {
      illnesses[illness] = {
        occur: personalIllnessesInfo[illness][0],
        happen: personalIllnessesInfo[illness][1],
        resolve: personalIllnessesInfo[illness][2],
      };
    }

    for (let illness of siblingIllnesses) {
      illnessesFam[illness] = {
        relation: siblingIllnessesInfo[illness][0],
        occur: siblingIllnessesInfo[illness][1],
        happen: siblingIllnessesInfo[illness][2],
        resolve: siblingIllnessesInfo[illness][3],
      };
    }

    return {
      history: {
        logOn: new Date().toLocaleString(),
        //medical
        medicalHistory: {
          leftEar: getEarConditions()[leftEar - 1],
          rightEar: getEarConditions()[rightEar - 1],
        },
        illnesses,
        //development
        ageSpeak: development,
        //education
        highestEdu: education,
        //family
        illnessesFam,
      },
    };
  };

  handleSubmit = async () => {
    const { updateUser, user } = this.props.context;
    const { leftEar, rightEar } = this.state;
    try {
      await axios.post("/api/listener/addLog", {
        listenerID: user,
        newLog: this.parseLog(),
        flag: "leftEarIsWorse",
        flagValue: leftEar <= rightEar,
      });
      await axios.post("/api/listener/addLog", {
        listenerID: user,
        newLog: "",
        flag: "historyDone",
        flagValue: true,
      });
    } catch (error) {
      alert(error.message);
      console.log(error);
    }
    sessionStorage.setItem("leftEarIsWorse", leftEar <= rightEar);
    sessionStorage.setItem("historyDone", true);
    updateUser();
  };

  getStepContent = (step) => {
    const { classes } = this.props;
    const {
      leftEar,
      rightEar,
      development,
      education,
      personalIllnesses,
      siblingIllnesses,
      personalIllnessesInfo,
      siblingIllnessesInfo,
    } = this.state;

    switch (step) {
      case 0:
        return (
          <Grid container spacing={1}>
            <Grid item xs={12}>
              How well are you hearing?
            </Grid>
            <Grid item xs={6}>
              <FormControl required className={classes.formControl}>
                <InputLabel>Left Ear</InputLabel>
                <Select
                  native
                  value={leftEar}
                  onChange={this.handleChangeEarInfo("leftEar")}
                >
                  <option value="" />
                  <option value={1}>No Hearing</option>
                  <option value={2}>Limited Hearing</option>
                  <option value={3}>Normal Hearing</option>
                </Select>
                <FormHelperText>Required</FormHelperText>
              </FormControl>
            </Grid>
            <Grid item xs={6}>
              <FormControl required className={classes.formControl}>
                <InputLabel>Right Ear</InputLabel>
                <Select
                  native
                  value={rightEar}
                  onChange={this.handleChangeEarInfo("rightEar")}
                >
                  <option value="" />
                  <option value={1}>No Hearing</option>
                  <option value={2}>Limited Hearing</option>
                  <option value={3}>Normal Hearing</option>
                </Select>
                <FormHelperText>Required</FormHelperText>
              </FormControl>
            </Grid>
            <Grid item xs={12}>
              Check the following illnesses you have had before:
            </Grid>
            <Grid item xs={12}>
              <FormGroup row>
                {getIllnesses().map((illness) => {
                  return (
                    <FormControlLabel
                      key={`${illness}-checkbox`}
                      control={
                        <Checkbox
                          className={classes.illnessCheckBox}
                          checked={personalIllnesses.includes(illness)}
                          onChange={this.handleChangeIllness(illness, false)}
                          value={illness}
                        />
                      }
                      label={illness}
                    />
                  );
                })}
              </FormGroup>
            </Grid>
            <Grid item xs={12}>
              {personalIllnesses.map((illness) => {
                return (
                  <div key={`${illness}-form`}>
                    <form noValidate autoComplete="off">
                      <TextField
                        key={`${illness}-text1`}
                        label={`When did ${illness} occur?`}
                        fullWidth
                        margin="dense"
                        multiline
                        onChange={this.handleChangeIllnessInfo(
                          illness,
                          false,
                          0
                        )}
                        value={personalIllnessesInfo[illness][0]}
                      />
                      <TextField
                        key={`${illness}-text2`}
                        label={`How did ${illness} happen?`}
                        fullWidth
                        margin="dense"
                        multiline
                        onChange={this.handleChangeIllnessInfo(
                          illness,
                          false,
                          1
                        )}
                        value={personalIllnessesInfo[illness][1]}
                      />
                      <TextField
                        key={`${illness}-text3`}
                        label={`When did ${illness} resolve?`}
                        fullWidth
                        margin="dense"
                        multiline
                        onChange={this.handleChangeIllnessInfo(
                          illness,
                          false,
                          2
                        )}
                        value={personalIllnessesInfo[illness][2]}
                      />
                    </form>
                  </div>
                );
              })}
            </Grid>
          </Grid>
        );
      case 1:
        return (
          <Grid container spacing={0}>
            <Grid item xs={12}>
              <FormControl>
                <InputLabel className={classes.inputLabel}>
                  Age you started to speak?
                </InputLabel>
                <Select
                  className={classes.fullSelect}
                  autoWidth={true}
                  native
                  value={development}
                  onChange={this.handleChangeUserInfo("development")}
                >
                  <option value="" />
                  <option value="0 - 2 years old.">0 - 2 years old.</option>
                  <option value="After 2 years old.">After 2 years old.</option>
                  <option value="Unknown.">Unknown.</option>
                </Select>
              </FormControl>
            </Grid>
          </Grid>
        );
      case 2:
        return (
          <Grid container spacing={0}>
            <Grid item xs={12}>
              <FormControl>
                <InputLabel className={classes.inputLabel}>
                  The highest education level you obtained?
                </InputLabel>
                <Select
                  className={classes.fullSelect}
                  autoWidth={true}
                  native
                  value={education}
                  onChange={this.handleChangeUserInfo("education")}
                >
                  <option value="" />
                  <option value="Primary, Secondary and Higher Secondary education.">
                    Primary, Secondary and Higher Secondary education.
                  </option>
                  <option value="Under-Graduate/Bachelor’s level education.">
                    Under-Graduate/Bachelor’s level education.
                  </option>
                  <option value="Post-Graduate/Master’s level education.">
                    Post-Graduate/Master’s level education.
                  </option>
                  <option value="Doctoral studies/Ph.D level education.">
                    Doctoral studies/Ph.D level education.
                  </option>
                  <option value="Vocational Education & Training.">
                    Vocational Education & Training.
                  </option>
                  <option value="Certificate and Diploma programs.">
                    Certificate and Diploma programs.
                  </option>
                  <option value="Distance Education.">
                    Distance Education.
                  </option>
                </Select>
              </FormControl>
            </Grid>
          </Grid>
        );
      case 3:
        return (
          <Grid container spacing={1}>
            <Grid item xs={12}>
              Have any of your family members had the following illnesses?
            </Grid>
            <Grid item xs={12}>
              <FormGroup row>
                {getIllnesses().map((illness) => {
                  return (
                    <FormControlLabel
                      key={`${illness}-checkbox`}
                      control={
                        <Checkbox
                          className={classes.illnessCheckBox}
                          checked={siblingIllnesses.includes(illness)}
                          onChange={this.handleChangeIllness(illness, true)}
                          value={illness}
                        />
                      }
                      label={illness}
                    />
                  );
                })}
              </FormGroup>
            </Grid>
            <Grid item xs={12}>
              {siblingIllnesses.map((illness) => {
                return (
                  <div key={`${illness}-form`}>
                    {`${illness}: Relation(s) between you and the person(s)?`}
                    <FormControl>
                      <FormGroup row>
                        {getRelations().map((relation) => {
                          return (
                            <FormControlLabel
                              className={classes.relationCheckBox}
                              key={`${relation}-checkbox`}
                              control={
                                <Checkbox
                                  checked={siblingIllnessesInfo[
                                    illness
                                  ][0].includes(relation)}
                                  onChange={this.handleChangeIllnessInfo(
                                    illness,
                                    true,
                                    0
                                  )}
                                  value={relation}
                                />
                              }
                              label={relation}
                            />
                          );
                        })}
                      </FormGroup>
                    </FormControl>
                    <form noValidate autoComplete="off">
                      <TextField
                        key={`${illness}-text4`}
                        label={`When did ${illness} occur?`}
                        fullWidth
                        margin="dense"
                        multiline
                        onChange={this.handleChangeIllnessInfo(
                          illness,
                          true,
                          1
                        )}
                        value={siblingIllnessesInfo[illness][1]}
                      />
                      <TextField
                        key={`${illness}-text5`}
                        label={`How did ${illness} happen?`}
                        fullWidth
                        margin="dense"
                        multiline
                        onChange={this.handleChangeIllnessInfo(
                          illness,
                          true,
                          2
                        )}
                        value={siblingIllnessesInfo[illness][2]}
                      />
                      <TextField
                        key={`${illness}-text6`}
                        label={`When did ${illness} resolve?`}
                        fullWidth
                        margin="dense"
                        multiline
                        onChange={this.handleChangeIllnessInfo(
                          illness,
                          true,
                          3
                        )}
                        value={siblingIllnessesInfo[illness][3]}
                      />
                    </form>
                  </div>
                );
              })}
            </Grid>
          </Grid>
        );
      default:
        return "Unknown step";
    }
  };

  render() {
    const { classes } = this.props;
    const { activeStep, leftEar, rightEar } = this.state;

    const steps = getSteps();

    return (
      <Paper>
        <Stepper activeStep={activeStep} orientation="vertical">
          {steps.map((label, index) => (
            <Step key={label}>
              <StepLabel>{label}</StepLabel>
              <StepContent>
                <Typography component={"span"}>
                  {this.getStepContent(index)}
                </Typography>
                <div className={classes.actionsContainer}>
                  <div>
                    <Button
                      disabled={activeStep === 0}
                      onClick={this.handleBack}
                      className={classes.button}
                    >
                      Back
                    </Button>
                    {activeStep === steps.length - 1 ? (
                      <Button
                        disabled={leftEar === "" || rightEar === ""}
                        variant="contained"
                        color="primary"
                        onClick={this.handleSubmit}
                        className={classes.button}
                        component={Link}
                        to="/researcherpage"
                      >
                        Submit
                      </Button>
                    ) : (
                      <Button
                        disabled={leftEar === "" || rightEar === ""}
                        variant="contained"
                        color="primary"
                        onClick={this.handleNext}
                        className={classes.button}
                      >
                        Next
                      </Button>
                    )}
                  </div>
                </div>
              </StepContent>
            </Step>
          ))}
        </Stepper>
      </Paper>
    );
  }
}

export default compose(
  withUserAuth,
  withRouter,
  withStyles(styles)
)(SurveyPage);
