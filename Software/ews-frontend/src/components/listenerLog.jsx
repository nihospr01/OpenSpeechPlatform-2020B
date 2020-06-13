import React, { Component } from "react";
import { withStyles } from "@material-ui/styles";
import Typography from "@material-ui/core/Typography";
import DialogTitle from "@material-ui/core/DialogTitle";
import DialogActions from "@material-ui/core/DialogActions";
import DialogContent from "@material-ui/core/DialogContent";
import { withRouter } from "react-router-dom";
import { compose } from "recompose";
import withUserAuth from "context/withUserAuth";

import Tabs from "@material-ui/core/Tabs";
import Tab from "@material-ui/core/Tab";
import PropTypes from "prop-types";
import Box from "@material-ui/core/Box";
import List from "@material-ui/core/List";
import ListItem from "@material-ui/core/ListItem";
import ListItemText from "@material-ui/core/ListItemText";

import {
  illnessQuestions,
  illnessQuestionsFam,
  AFCQuestions,
  PTAChannels,
} from "./listenerLogConstant";

function TabPanel(props) {
  const { children, value, index, ...other } = props;

  return (
    <Typography
      component="div"
      role="tabpanel"
      hidden={value !== index}
      id={`simple-tabpanel-${index}`}
      aria-labelledby={`simple-tab-${index}`}
      {...other}
    >
      {value === index && <Box p={3}>{children}</Box>}
    </Typography>
  );
}

TabPanel.propTypes = {
  children: PropTypes.node,
  index: PropTypes.any.isRequired,
  value: PropTypes.any.isRequired,
};

function InfoRow(props) {
  const { title, content, ...other } = props;

  return (
    <Typography component="div" variant="body1" color="textSecondary">
      {title}
      <Typography
        component="span"
        variant="body1"
        color="textPrimary"
        style={{ marginLeft: 5 }}
      >
        {content}
      </Typography>
    </Typography>
  );
}
InfoRow.propTypes = {
  title: PropTypes.any.isRequired,
  content: PropTypes.any.isRequired,
  // key: PropTypes.any.number
};

const styles = (theme) => ({
  root: {
    flexGrow: 1,
  },
  content: {
    padding: theme.spacing(3),
  },
  listenerSection: {
    marginTop: 30,
    width: "90%",
    maxWidth: 1000,
  },
  listenerSectionContent: {
    display: "flex",
    flexDirection: "column",
    alignItems: "center",
  },
  topBarButton: {
    marginLeft: 30,
  },
  textField: {
    width: "90%",
    maxWidth: 400,
    marginBottom: 20,
  },
});

const tabs = ["Patient History", "4AFC", "PTA", "Assessment"];
class ListenerLog extends Component {
  state = {
    currTabIdx: 0,
    listenerLog: this.props.log,
  };

  handleTabChange = (event, currTabIdx) => {
    this.setState({
      currTabIdx,
    });
  };

  renderMedicalHistory = (history) => (
    <ListItemText
      primary={
        <React.Fragment>
          <Typography variant="h6" color="textPrimary">
            Medical History
          </Typography>
        </React.Fragment>
      }
      disableTypography
      secondary={
        <React.Fragment>
          <InfoRow
            title={"Left ear Condition:"}
            content={history.medicalHistory.leftEar}
          />
          <InfoRow
            title={"Right ear Condition:"}
            content={history.medicalHistory.rightEar}
          />
          <Typography component="h6" variant="body1" color="textSecondary">
            {"Illnesses had before:"}
          </Typography>
          {Object.keys(history.illnesses).map((illness, index) => (
              <>
                <Typography component="h6" variant="body1" color="textPrimary">
                  {illness}
                </Typography>
                {Object.keys(illnessQuestions).map((key, index_q) => (
                  <InfoRow
                    key={`illness${index}_question${index_q}`}
                    title={illnessQuestions[key]}
                    content={history.illnesses[illness][key]}
                  />
                ))}
              </>
            ))}
        </React.Fragment>
      }
    />
  );

  renderEducationHistory = (history) => (
    <ListItemText
      disableTypography
      primary={
        <React.Fragment>
          <Typography variant="h6" color="textPrimary">
            Family History
          </Typography>
        </React.Fragment>
      }
      secondary={
        <React.Fragment>
          <Typography component="h6" variant="body1" color="textSecondary">
            {"Illnesses had before:"}
            {Object.keys(history.illnessesFam).map((illness, index) => (
              <>
                <Typography component="div" variant="body1" color="textPrimary">
                  {illness}
                </Typography>
                {Object.keys(illnessQuestionsFam).map((key, index_q) => (
                  <InfoRow
                    key={`illness${index}_question${index_q}`}
                    title={illnessQuestionsFam[key]}
                    content={history.illnessesFam[illness][key]}
                  />
                ))}
              </>
            ))}
          </Typography>
        </React.Fragment>
      }
    />
  );

  render4AFC = () => {
    const { AFC } = this.state.listenerLog;
    return (
      AFC && (
        <>
          <ListItem divider>
            <ListItemText
              primary={
                <Typography variant="h6" color="textPrimary">
                  {"4AFC Log on:  " + AFC.logOn}
                </Typography>
              }
            />
          </ListItem>
          {AFC.log.map((curr, index) => (
            <ListItem divider>
              <ListItemText
                disableTypography
                primary={
                  <Typography variant="h6" color="textPrimary">
                    {`Step ${index + 1}`}
                  </Typography>
                }
                secondary={
                  <>
                    {Object.keys(AFCQuestions).map((key, index_q) => (
                      <InfoRow
                        key={`step${index}_question${index_q}`}
                        title={AFCQuestions[key]}
                        content={
                          key === "currentOptions"
                            ? curr[key].join(" ")
                            : curr[key]
                        }
                      />
                    ))}
                  </>
                }
              />
            </ListItem>
          ))}
        </>
      )
    );
  };

  renderPTA = () => {
    const { PTA } = this.state.listenerLog;

    return (
      PTA && (
        <>
          <ListItem divider>
            <ListItemText
              primary={
                <Typography variant="h6" color="textPrimary">
                  {"PTA Log on:  " + PTA.logOn}
                </Typography>
              }
            />
          </ListItem>
          <ListItem divider>
            <ListItemText
              primary={
                <Typography variant="h6" color="textPrimary">
                  {"Audiologist ID:  " + PTA.audiologistID}
                </Typography>
              }
            />
          </ListItem>
          {Object.keys(PTAChannels).map((channel, index) => (
            <ListItem index={`channel${index}`} divider>
              <ListItemText
                disableTypography
                primary={
                  <Typography variant="h6" color="textPrimary">
                    {PTAChannels[channel]}
                  </Typography>
                }
                secondary={Object.keys(PTA[channel]).map((key, index_f) => (
                  <InfoRow
                    index={`channel${index}_freq${index_f}`}
                    title={`${key}:`}
                    content={
                      PTA[channel][key] === "" ? "N/A" : PTA[channel][key]
                    }
                  />
                ))}
              />
            </ListItem>
          ))}
        </>
      )
    );
  };

  renderAssessment = () => {
    const { assessment } = this.state.listenerLog;
    return (
      assessment && (
        <>
          <ListItem divider>
            <ListItemText
              primary={
                <Typography variant="h6" color="textPrimary">
                  {"User Assessment Log on:  " + assessment.logOn}
                </Typography>
              }
            />
          </ListItem>
          <ListItem divider>
            <ListItemText
              secondary={
                <>
                  {assessment.log.map((res, index) => (
                    <InfoRow
                      key={index}
                      title={`Step ${index + 1}:`}
                      content={res}
                    />
                  ))}
                </>
              }
            />
          </ListItem>
        </>
      )
    );
  };

  renderPatientHistory = () => {
    const { history } = this.state.listenerLog;
    return (
      <List>
        <ListItem divider>
          <ListItemText
            primary={
              <Typography variant="h6" color="textPrimary">
                {"Patient History Log on:  " + history.logOn}
              </Typography>
            }
          />
        </ListItem>
        <ListItem divider>{this.renderMedicalHistory(history)}</ListItem>
        <ListItem divider>
          <ListItemText
            primary={
              <React.Fragment>
                <Typography variant="h6" color="textPrimary">
                  Development History
                </Typography>
              </React.Fragment>
            }
            secondary={
              <InfoRow 
              title={"Age started to speak:"}
              content= {history.ageSpeak}
            />
            }
          />
        </ListItem>
        <ListItem divider>
          <ListItemText
            disableTypography
            primary={
              <React.Fragment>
                <Typography variant="h6" color="textPrimary">
                  Education History
                </Typography>
              </React.Fragment>
            }
            secondary={
              <InfoRow 
                title={"The highest education obtained:"}
                content= {history.highestEdu}
              />
            }
          />
        </ListItem>
        <ListItem>{this.renderEducationHistory(history)}</ListItem>
      </List>
    );
  };
  render() {
    const { currTabIdx } = this.state;
    console.log(this.props.open);

    return (
      <>
        <DialogTitle>
          <Tabs
            value={currTabIdx}
            onChange={this.handleTabChange}
            indicatorColor="primary"
            textColor="primary"
            centered
          >
            {tabs.map((tab, currTabIdx) => (
              <Tab label={tab} key={currTabIdx} />
            ))}
          </Tabs>
        </DialogTitle>
        <DialogContent>
          <TabPanel
            value={currTabIdx}
            index={0}
            style={{
              whiteSpace: "pre-line",
            }}
          >
            {this.renderPatientHistory()}
          </TabPanel>
          <TabPanel value={currTabIdx} index={1}>
            {this.render4AFC()}
          </TabPanel>
          <TabPanel value={currTabIdx} index={2}>
            {this.renderPTA()}
          </TabPanel>
          <TabPanel value={currTabIdx} index={3}>
            {this.renderAssessment()}
          </TabPanel>
        </DialogContent>
      </>
    );
  }
}

export default compose(
  withUserAuth,
  withRouter,
  withStyles(styles)
)(ListenerLog);
