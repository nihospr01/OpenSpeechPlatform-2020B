import images from './images/hearingLossDemo';

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
//[option1, option2, option3]
export const optionDescriptions = [
  'The audiograms are classified to different types by classification of the range of the worst hearing loss level corresponding to the seven frequencies 500,1000,2000,3000,4000,6000,8000 Hz. The node occupancy of Normal (Range: <25dB) - 17584 profiles , Mild (Range: 25-40 dB) - 4842 profiles , Medium (Range: 40-70 dB) - 3350 profiles,  Severe (Range: 70-80 dB) - 2675 profiles, Profound (>80 dB) - 1404 profiles.',
  'The audiograms are classified to different types by classification of the range of the average hearing loss level corresponding to the four frequencies 500,1000,2000 and 4000 Hz. The node occupancy of Normal (Range: <25dB) - 23444 profiles , Mild (Range: 25-40 dB) - 3337 profiles , Medium (Range: 40-70 dB) - 2895 profiles,  Severe (Range: 70-80 dB) - 146 profiles, Profound (>80 dB) - 33 profiles.',
  'The audiograms are classified to different types by classification of the range of the average hearing loss levels corresponding to the seven frequencies 500,1000,2000,3000,4000,6000,8000 Hz. The node occupancy of Normal (Range: <25dB) - 25685 profiles , Mild (Range: 25-40 dB) - 2709 profiles , Medium (Range: 40-70 dB) - 1373 profiles,  Severe (Range: 70-80 dB) - 70 profiles, Profound (>80 dB) - 18 profiles.',
];

export const optionImages = [
  images.option1,
  images.option2,
  images.option3
]

export const optionOccupancies = [
  images.option1_occupancy,
  images.option2_occupancy,
  images.option3_occupancy
]
//[normal, mild, medium, severe, profound]
const option1_levels = [
  images.option1_normal,
  images.option1_mild,
  images.option1_medium,
  images.option1_severe,
  images.option1_profound
]
const option2_levels = [
  images.option2_normal,
  images.option2_mild,
  images.option2_medium,
  images.option2_severe,
  images.option2_profound
]
const option3_levels = [
  images.option3_normal,
  images.option3_mild,
  images.option3_medium,
  images.option3_severe,
  images.option3_profound
]

export const roundOff4 = (value) => {
    return value * 10000 / 10000;
}

export const optionLossImages = [option1_levels, option2_levels, option3_levels]