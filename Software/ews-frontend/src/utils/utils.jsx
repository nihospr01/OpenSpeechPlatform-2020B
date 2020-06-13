export const logBoarder =
  "==============================================================================\n";
export const logSubBoarder =
  "------------------------------------------------------------------------------\n";
export const axios = require("axios");

// To add new band, put in a if statement with the desired bandNumber
export const getBandFrequencies = (bandNumber) => {
  if (bandNumber === 6) {
    return ["250", "500", "1000", "2000", "4000", "8000", "All"];
  }
  if (bandNumber === 10) {
    return [
      "250",
      "500",
      "750",
      "1000",
      "1500",
      "2000",
      "3000",
      "4000",
      "6000",
      "8000",
      "All",
    ];
  }
};

export const highlightColor = "#f50057";

export const AFCLogHeader = [
  "ListenerID",
  "4AFC Log on",
  "Question 1 options",
  "Correct answer",
  "Selected answer",
  "Question 2 options",
  "Correct answer",
  "Selected answer",
  "Question 3 options",
  "Correct answer",
  "Selected answer",
  "Question 4 options",
  "Correct answer",
  "Selected answer",
  "Question 5 options",
  "Correct answer",
  "Selected answer\n",
];

export const PatientHistoryHeader = [
  "ListenerId",
  "Patient History Log on",
  "Left ear Condition",
  "Right ear Condition",
  "Illnesses had before",
  "When did it occur",
  "How did it happen",
  "When did it resolve",
  "Age started to speak",
  "The highest education obtained",
  "Illnesses family members had before",
  "Relation(s)",
  "When did it occur",
  "How did it happen",
  "When did it resolve", "\n"
];
