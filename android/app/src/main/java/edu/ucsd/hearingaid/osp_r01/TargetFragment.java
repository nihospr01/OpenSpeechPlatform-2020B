package edu.ucsd.hearingaid.osp_r01;

import android.app.Dialog;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.NumberPicker;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import java.text.DateFormat;
import java.util.Arrays;
import java.util.Date;

public class TargetFragment extends Fragment implements View.OnClickListener {
    private final int NUM_BANDS = 6;

    private TextView controlsText = null;
    private Button transmitButton = null;
    private Button populateButton = null;

    private String[] gainValRange, compRatioValRange, inputHalfOctaveRange, outputHalfOctaveRange;
    private int[] compRatioIdx = new int[NUM_BANDS];
    private static final int[] crButtonIDS = {R.id.target_cr_1, R.id.target_cr_2, R.id.target_cr_3,
            R.id.target_cr_4, R.id.target_cr_5, R.id.target_cr_6};
    private Button[] crButtons = new Button[crButtonIDS.length];
    private static final int[] g50ButtonIDS = {R.id.target_g50_1, R.id.target_g50_2, R.id.target_g50_3,
            R.id.target_g50_4, R.id.target_g50_5, R.id.target_g50_6};
    private Button[] g50Buttons = new Button[g50ButtonIDS.length];
    private static final int[] g65ButtonIDS = {R.id.target_g65_1, R.id.target_g65_2, R.id.target_g65_3,
            R.id.target_g65_4, R.id.target_g65_5, R.id.target_g65_6};
    private Button[] g65Buttons = new Button[g65ButtonIDS.length];
    private static final int[] g80ButtonIDS = {R.id.target_g80_1, R.id.target_g80_2, R.id.target_g80_3,
            R.id.target_g80_4, R.id.target_g80_5, R.id.target_g80_6};
    private Button[] g80Buttons = new Button[g80ButtonIDS.length];

    private static final int[] inputOctaveIDS = {R.id.target_input_octave_1, R.id.target_input_octave_2, R.id.target_input_octave_3,
            R.id.target_input_octave_4, R.id.target_input_octave_5, R.id.target_input_octave_7};
    private Button[] inputOctaveButtons = new Button[inputOctaveIDS.length];

    private static final int[] outputOctaveIDS = {R.id.target_output_octave_1, R.id.target_output_octave_2, R.id.target_output_octave_3,
            R.id.target_output_octave_4, R.id.target_output_octave_5, R.id.target_output_octave_6};
    private Button[] outputOctaveButtons = new Button[outputOctaveIDS.length];

    private static final int[] inputHalfOctaveIDS = {R.id.target_input_half_octave_1, R.id.target_input_half_octave_2, R.id.target_input_half_octave_3,
            R.id.target_input_half_octave_4, R.id.target_input_half_octave_5, R.id.target_input_half_octave_6,R.id.target_input_half_octave_7,
            R.id.target_input_half_octave_8,R.id.target_input_half_octave_9};
    private Button[] inputHalfOctaveButtons = new Button[inputHalfOctaveIDS.length];

    private static final int[] outputHalfOctaveIDS = {R.id.target_output_half_octave_1, R.id.target_output_half_octave_2, R.id.target_output_half_octave_3,
            R.id.target_output_half_octave_4, R.id.target_output_half_octave_5, R.id.target_output_half_octave_6, R.id.target_output_half_octave_7,
            R.id.target_output_half_octave_8, R.id.target_output_half_octave_9};
    private Button[] outputHalfOctaveButtons = new Button[outputHalfOctaveIDS.length];
    private static final int[] freqButtonIds = {R.id.target_f1, R.id.target_f2,
            R.id.target_f3, R.id.target_f4, R.id.target_f5, R.id.target_f6};
    private TextView[] freqButtons = new TextView[freqButtonIds.length];

    private int[] filterCFreq;
    private float[] compRatio;
    private int[] g50;
    private int[] g65;
    private int[] g80;
    private int[] inputHalfOctave = {59,59,59,57,54,51,48,45,42,42,42}; //boothroydValues
    private int[] outputHalfOctave = {0,50,0,55,70,75,80,75,75,70,65}; //sample output verifit values

    private int[] inputOctaveValues = new int[inputOctaveIDS.length];
    private int[] outputOctaveValues = new int[outputOctaveIDS.length];




    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        View v = inflater.inflate(R.layout.fragment_target, container, false);

        controlsText = (TextView) v.findViewById(R.id.target_controls_text);
        gainValRange = range(0, 100);
        inputHalfOctaveRange = range(0,100);
        outputHalfOctaveRange = range(0,200);

        compRatioValRange = getResources().getStringArray(R.array.comp_ratio_vals);

        compRatio = OspInterface.getInstance().getCompRatio();
        g50 = OspInterface.getInstance().getG50();
        g65 = OspInterface.getInstance().getG65();
        g80 = OspInterface.getInstance().getG80();
        filterCFreq = OspInterface.getInstance().getCFreq();

        for (int i = 0; i < inputOctaveIDS.length; i++) {
            final int b = i;
            inputOctaveButtons[b] = (Button) v.findViewById(inputOctaveIDS[b]);
            inputOctaveButtons[b].setText(String.valueOf(inputOctaveValues[b]));
            inputOctaveButtons[b].setOnClickListener(this);
            inputOctaveButtons[b].setEnabled(false);

        }

        for (int i = 0; i < outputOctaveIDS.length; i++) {
            final int b = i;
            outputOctaveButtons[b] = (Button) v.findViewById(outputOctaveIDS[b]);
            outputOctaveButtons[b].setText(String.valueOf(outputOctaveValues[b]));
            outputOctaveButtons[b].setOnClickListener(this);
            outputOctaveButtons[b].setEnabled(false);
        }


        for (int i = 0; i < inputHalfOctaveIDS.length; i++) {
            final int b = i;
            inputHalfOctaveButtons[b] = (Button) v.findViewById(inputHalfOctaveIDS[b]);
            inputHalfOctaveButtons[b].setOnClickListener(this);
            inputHalfOctaveButtons[b].setText(String.valueOf(inputHalfOctave[b+2]));
            outputHalfOctaveButtons[b] = (Button) v.findViewById(outputHalfOctaveIDS[b]);
            outputHalfOctaveButtons[b].setOnClickListener(this);
            outputHalfOctaveButtons[b].setText(String.valueOf(outputHalfOctave[b+2]));
        }

        inputHalfOctaveButtons[0].setText(String.valueOf(inputHalfOctave[1]));
        inputHalfOctaveButtons[1].setText(String.valueOf(inputHalfOctave[3]));
        outputHalfOctaveButtons[0].setText(String.valueOf(outputHalfOctave[1]));
        outputHalfOctaveButtons[1].setText(String.valueOf(outputHalfOctave[3]));




        for (int i = 0; i < crButtonIDS.length; i++) {
            final int b = i;
            crButtons[b] = (Button) v.findViewById(crButtonIDS[b]);
            crButtons[b].setText(String.valueOf(compRatio[b]));
            crButtons[b].setOnClickListener(this);
            g50Buttons[b] = (Button) v.findViewById(g50ButtonIDS[b]);
            g50Buttons[b].setText(String.valueOf(g50[b]));
            g50Buttons[b].setOnClickListener(this);
            g65Buttons[b] = (Button) v.findViewById(g65ButtonIDS[b]);
            g65Buttons[b].setText(String.valueOf(g65[b]));
            g65Buttons[b].setOnClickListener(this);
            g80Buttons[b] = (Button) v.findViewById(g80ButtonIDS[b]);
            g80Buttons[b].setText(String.valueOf(g80[b]));
            g80Buttons[b].setOnClickListener(this);
            freqButtons[b] = (TextView) v.findViewById(freqButtonIds[b]);
            freqButtons[b].setText(String.valueOf(filterCFreq[b]));
        }

        RadioGroup mRadioGrp = (RadioGroup) v.findViewById(R.id.target_radio_group);
        mRadioGrp.check(R.id.target_radio_g50);
        disableCRG65Rows();
        enableG50G80Rows();
        mRadioGrp.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
            public void onCheckedChanged(RadioGroup group, int checkedId) {
                // checkedId is the RadioButton selected
                switch (checkedId) {
                    case R.id.target_radio_comp:
                        disableG50G80Rows();
                        enableCRG65Rows();
                        break;
                    case R.id.target_radio_g50:
                        disableCRG65Rows();
                        enableG50G80Rows();
                        break;
                }
            }
        });

        transmitButton = (Button) v.findViewById(R.id.target_transmit_btn);
        transmitButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                OspInterface.getInstance().setPageName("Target Values");
                OspInterface.getInstance().setButtonName(getResources().getString(R.string.transmit));
                sendMessage();
            }
        });

        populateButton = (Button) v.findViewById(R.id.target_populate_btn);
        populateButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                // TODO: interpolation required convert to function??
                interpolateOctaveValues();
                for (int i = 0; i < g65ButtonIDS.length; i++) {
                    final int b = i;
                    g65[b] = outputOctaveValues[b] - inputOctaveValues[b];
                    g65Buttons[b].setText(String.valueOf(g65[b]));
                    OspInterface.getInstance().setG65(g65[b], b);
                    updateG50G80(b);
                }
                setControlsText();
            }
        });


        setControlsText();
        return v;
    }

    private void interpolateOctaveValues() {
        outputOctaveValues[0] = outputHalfOctave[1] + 3; //since we don't know o/p verifit dB value at 177Hz, value at 250Hz is used.
        outputOctaveValues[1] = (outputHalfOctave[1]+ outputHalfOctave[3])/2 + 3; //since we don't know o/p verifit dB value at 177Hz, value at 250Hz is used.
        for (int i = 0; i < inputOctaveValues.length; i++) {
            inputOctaveValues[i] = inputHalfOctave[i*2];
            inputOctaveButtons[i].setText(String.valueOf(inputOctaveValues[i]));
            if (i>1) {
                outputOctaveValues[i] = outputHalfOctave[i * 2] + 3;
            }
            outputOctaveButtons[i].setText(String.valueOf(outputOctaveValues[i]));
        }

    }

    private void enableG50G80Rows() {
        for (int i = 0; i < g50ButtonIDS.length; i++) {
            g50Buttons[i].setEnabled(true);
        }
        for (int i = 0; i < g80ButtonIDS.length; i++) {
            g80Buttons[i].setEnabled(true);
        }
    }

    private void enableCRG65Rows() {
        for (int i = 0; i < crButtonIDS.length; i++) {
            crButtons[i].setEnabled(true);
        }
        for (int i = 0; i < g65ButtonIDS.length; i++) {
            g65Buttons[i].setEnabled(true);
        }
    }

    private void disableCRG65Rows() {
        for (int i = 0; i < crButtonIDS.length; i++) {
            crButtons[i].setEnabled(false);
        }
        for (int i = 0; i < g65ButtonIDS.length; i++) {
            g65Buttons[i].setEnabled(false);
        }
    }

    private void disableG50G80Rows() {
        for (int i = 0; i < g50ButtonIDS.length; i++) {
            g50Buttons[i].setEnabled(false);
        }
        for (int i = 0; i < g80ButtonIDS.length; i++) {
            g80Buttons[i].setEnabled(false);
        }
    }


    public static TargetFragment newInstance() {
        TargetFragment f = new TargetFragment();
        return f;
    }


    public void setControlsText() {
        String s1 = String.format("Fullness = %d, Volume = %d, Crispness = %d\n\n",
                OspInterface.getInstance().getFullness(), OspInterface.getInstance().getVolume(),
                OspInterface.getInstance().getCrispness());
        String s2 = String.format("Gain 50 = %s\nGain 65 = %s\nGain 80 = %s\nCompression Ratio = %s\n" +
                        "Knee Low = %s\nMPO Limit = %s\nAttack Time = %s\nRelease Time = %s",
                Arrays.toString(OspInterface.getInstance().getG50()), Arrays.toString(OspInterface.getInstance().getG65()),
                Arrays.toString(OspInterface.getInstance().getG80()), Arrays.toString(OspInterface.getInstance().getCompRatio()),
                Arrays.toString(OspInterface.getInstance().getKneeLow()), Arrays.toString(OspInterface.getInstance().getMPOLimit()),
                Arrays.toString(OspInterface.getInstance().getAttackTime()), Arrays.toString(OspInterface.getInstance().getReleaseTime()));
        controlsText.setText(s1 + s2);
    }

    public String[] range(int start, int stop) {
        int[] result = new int[stop - start];

        for (int i = 0; i < stop - start; i++)
            result[i] = start + i;

        String[] a = Arrays.toString(result).split("[\\[\\]]")[1].split(", ");
        return a;
    }
    public void onClick(View v) {
        switch (v.getId()) {

            case R.id.target_input_half_octave_1:
                showInputHalfOctaveDialog(v, 0);
                break;
            case R.id.target_input_half_octave_2:
                showInputHalfOctaveDialog(v, 1);
                break;
            case R.id.target_input_half_octave_3:
                showInputHalfOctaveDialog(v, 2);
                break;
            case R.id.target_input_half_octave_4:
                showInputHalfOctaveDialog(v, 3);
                break;
            case R.id.target_input_half_octave_5:
                showInputHalfOctaveDialog(v, 4);
                break;
            case R.id.target_input_half_octave_6:
                showInputHalfOctaveDialog(v, 5);
                break;
            case R.id.target_input_half_octave_7:
                showInputHalfOctaveDialog(v, 6);
                break;
            case R.id.target_input_half_octave_8:
                showInputHalfOctaveDialog(v, 7);
                break;
            case R.id.target_input_half_octave_9:
                showInputHalfOctaveDialog(v, 8);
                break;

            case R.id.target_output_half_octave_1:
                showOutputHalfOctaveDialog(v, 0);
                break;
            case R.id.target_output_half_octave_2:
                showOutputHalfOctaveDialog(v, 1);
                break;
            case R.id.target_output_half_octave_3:
                showOutputHalfOctaveDialog(v, 2);
                break;
            case R.id.target_output_half_octave_4:
                showOutputHalfOctaveDialog(v, 3);
                break;
            case R.id.target_output_half_octave_5:
                showOutputHalfOctaveDialog(v, 4);
                break;
            case R.id.target_output_half_octave_6:
                showOutputHalfOctaveDialog(v, 5);
                break;
            case R.id.target_output_half_octave_7:
                showOutputHalfOctaveDialog(v, 6);
                break;
            case R.id.target_output_half_octave_8:
                showOutputHalfOctaveDialog(v, 7);
                break;
            case R.id.target_output_half_octave_9:
                showOutputHalfOctaveDialog(v, 8);
                break;

            case R.id.target_cr_1:
                showCompRatioDialog(v, 0);
                break;
            case R.id.target_cr_2:
                showCompRatioDialog(v, 1);
                break;
            case R.id.target_cr_3:
                showCompRatioDialog(v, 2);
                break;
            case R.id.target_cr_4:
                showCompRatioDialog(v, 3);
                break;
            case R.id.target_cr_5:
                showCompRatioDialog(v, 4);
                break;
            case R.id.target_cr_6:
                showCompRatioDialog(v, 5);
                break;


            case R.id.target_g50_1:
                showGain50Dialog(v, 0);
                break;
            case R.id.target_g50_2:
                showGain50Dialog(v, 1);
                break;
            case R.id.target_g50_3:
                showGain50Dialog(v, 2);
                break;
            case R.id.target_g50_4:
                showGain50Dialog(v, 3);
                break;
            case R.id.target_g50_5:
                showGain50Dialog(v, 4);
                break;
            case R.id.target_g50_6:
                showGain50Dialog(v, 5);
                break;


            case R.id.target_g65_1:
                showGain65Dialog(v, 0);
                break;
            case R.id.target_g65_2:
                showGain65Dialog(v, 1);
                break;
            case R.id.target_g65_3:
                showGain65Dialog(v, 2);
                break;
            case R.id.target_g65_4:
                showGain65Dialog(v, 3);
                break;
            case R.id.target_g65_5:
                showGain65Dialog(v, 4);
                break;
            case R.id.target_g65_6:
                showGain65Dialog(v, 5);
                break;


            case R.id.target_g80_1:
                showGain80Dialog(v, 0);
                break;
            case R.id.target_g80_2:
                showGain80Dialog(v, 1);
                break;
            case R.id.target_g80_3:
                showGain80Dialog(v, 2);
                break;
            case R.id.target_g80_4:
                showGain80Dialog(v, 3);
                break;
            case R.id.target_g80_5:
                showGain80Dialog(v, 4);
                break;
            case R.id.target_g80_6:
                showGain80Dialog(v, 5);
                break;

            default:
                Log.i("OnClick", ((Button) v).getText().toString());
        }
    }

    private void showCompRatioDialog(final View callingView, final int bandIdx) {
        final Dialog dialog = new Dialog(getActivity());
        dialog.setContentView(R.layout.number_picker_dialog);
        final TextView numberPickerTitleView = (TextView) dialog.findViewById(R.id.number_picker_title);
        numberPickerTitleView.setText("Set Compression Ratio for Band-" + String.valueOf(bandIdx + 1));
        final NumberPicker numberPicker = (NumberPicker) dialog.findViewById(R.id.number_picker);
        numberPicker.setMinValue(0);
        numberPicker.setMaxValue(compRatioValRange.length - 1);
        numberPicker.setDisplayedValues(compRatioValRange);
        numberPicker.setValue(compRatioIdx[bandIdx]);
        numberPicker.setWrapSelectorWheel(true);

        Button okButton = (Button) dialog.findViewById(R.id.dialog_ok_btn);
        okButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                compRatioIdx[bandIdx] = numberPicker.getValue();
                String valStr = compRatioValRange[numberPicker.getValue()];
                ((Button) callingView).setText(valStr);
                compRatio[bandIdx] = Float.parseFloat(valStr);
                OspInterface.getInstance().setCompRatio(compRatio[bandIdx], bandIdx);
                updateG50G80(bandIdx);
                setControlsText();
                dialog.dismiss();
            }
        });

        Button cancelButton = (Button) dialog.findViewById(R.id.dialog_cancel_btn);
        cancelButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                dialog.dismiss();
            }
        });

        dialog.show();
    }

    private void showGain50Dialog(final View callingView, final int bandIdx) {
        final Dialog dialog = new Dialog(getActivity());
        dialog.setContentView(R.layout.number_picker_dialog);
        final TextView numberPickerTitleView = (TextView) dialog.findViewById(R.id.number_picker_title);
        numberPickerTitleView.setText("Set Gain at 50 dB for Band-" + String.valueOf(bandIdx + 1));
        final NumberPicker numberPicker = (NumberPicker) dialog.findViewById(R.id.number_picker);
        numberPicker.setMinValue(0);
        numberPicker.setMaxValue(gainValRange.length - 1);
        numberPicker.setDisplayedValues(gainValRange);
        int prevVal = Integer.parseInt(((Button) callingView).getText().toString());
        numberPicker.setValue(prevVal);
        numberPicker.setWrapSelectorWheel(true);

        Button okButton = (Button) dialog.findViewById(R.id.dialog_ok_btn);
        okButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String valStr = gainValRange[numberPicker.getValue()];
                ((Button) callingView).setText(valStr);
                g50[bandIdx] = Integer.parseInt(valStr);
                OspInterface.getInstance().setG50(g50[bandIdx], bandIdx);
                updateCompRatioG65(bandIdx);
                setControlsText();
                dialog.dismiss();
            }
        });

        Button cancelButton = (Button) dialog.findViewById(R.id.dialog_cancel_btn);
        cancelButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                dialog.dismiss();
            }
        });

        dialog.show();
    }

    private void showGain65Dialog(final View callingView, final int bandIdx) {
        final Dialog dialog = new Dialog(getActivity());
        dialog.setContentView(R.layout.number_picker_dialog);
        final TextView numberPickerTitleView = (TextView) dialog.findViewById(R.id.number_picker_title);
        numberPickerTitleView.setText("Set Gain at 65 dB for Band-" + String.valueOf(bandIdx + 1));
        final NumberPicker numberPicker = (NumberPicker) dialog.findViewById(R.id.number_picker);
        numberPicker.setMinValue(0);
        numberPicker.setMaxValue(gainValRange.length - 1);
        numberPicker.setDisplayedValues(gainValRange);
        int prevVal = Integer.parseInt(((Button) callingView).getText().toString());
        numberPicker.setValue(prevVal);
        numberPicker.setWrapSelectorWheel(true);

        Button okButton = (Button) dialog.findViewById(R.id.dialog_ok_btn);
        okButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String valStr = gainValRange[numberPicker.getValue()];
                ((Button) callingView).setText(valStr);
                g65[bandIdx] = Integer.parseInt(valStr);
                OspInterface.getInstance().setG65(g65[bandIdx], bandIdx);
                updateG50G80(bandIdx);
                setControlsText();
                dialog.dismiss();
            }
        });

        Button cancelButton = (Button) dialog.findViewById(R.id.dialog_cancel_btn);
        cancelButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                dialog.dismiss();
            }
        });

        dialog.show();
    }

    private void showGain80Dialog(final View callingView, final int bandIdx) {
        final Dialog dialog = new Dialog(getActivity());
        dialog.setContentView(R.layout.number_picker_dialog);
        final TextView numberPickerTitleView = (TextView) dialog.findViewById(R.id.number_picker_title);
        numberPickerTitleView.setText("Set Gain at 80 dB for Band-" + String.valueOf(bandIdx + 1));
        final NumberPicker numberPicker = (NumberPicker) dialog.findViewById(R.id.number_picker);
        numberPicker.setMinValue(0);
        numberPicker.setMaxValue(gainValRange.length - 1);
        numberPicker.setDisplayedValues(gainValRange);
        int prevVal = Integer.parseInt(((Button) callingView).getText().toString());
        numberPicker.setValue(prevVal);
        numberPicker.setWrapSelectorWheel(true);

        Button okButton = (Button) dialog.findViewById(R.id.dialog_ok_btn);
        okButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String valStr = gainValRange[numberPicker.getValue()];
                ((Button) callingView).setText(valStr);
                g80[bandIdx] = Integer.parseInt(valStr);
                OspInterface.getInstance().setG80(g80[bandIdx], bandIdx);
                updateCompRatioG65(bandIdx);
                setControlsText();
                dialog.dismiss();
            }
        });

        Button cancelButton = (Button) dialog.findViewById(R.id.dialog_cancel_btn);
        cancelButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                dialog.dismiss();
            }
        });

        dialog.show();
    }

    private void showInputHalfOctaveDialog(final View callingView, final int bandIdx) {
        final Dialog dialog = new Dialog(getActivity());
        dialog.setContentView(R.layout.number_picker_dialog);
        final TextView numberPickerTitleView = (TextView) dialog.findViewById(R.id.number_picker_title);
        numberPickerTitleView.setText("Set Half Octave Input values corresponding to 65dB Speech for Band-" + String.valueOf(bandIdx + 1));
        final NumberPicker numberPicker = (NumberPicker) dialog.findViewById(R.id.number_picker);
        numberPicker.setMinValue(0);
        numberPicker.setMaxValue(inputHalfOctaveRange.length - 1);
        numberPicker.setDisplayedValues(inputHalfOctaveRange);
        int prevVal = Integer.parseInt(((Button) callingView).getText().toString());
        numberPicker.setValue(prevVal);
        numberPicker.setWrapSelectorWheel(true);

        Button okButton = (Button) dialog.findViewById(R.id.dialog_ok_btn);
        okButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String valStr = inputHalfOctaveRange[numberPicker.getValue()];
                ((Button) callingView).setText(valStr);
                inputHalfOctave[bandIdx] = Integer.parseInt(valStr);
                //OspInterface.getInstance().setG80(g80[bandIdx], bandIdx);
                dialog.dismiss();
            }
        });

        Button cancelButton = (Button) dialog.findViewById(R.id.dialog_cancel_btn);
        cancelButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                dialog.dismiss();
            }
        });

        dialog.show();
    }

    private void showOutputHalfOctaveDialog(final View callingView, final int bandIdx) {
        final Dialog dialog = new Dialog(getActivity());
        dialog.setContentView(R.layout.number_picker_dialog);
        final TextView numberPickerTitleView = (TextView) dialog.findViewById(R.id.number_picker_title);
        numberPickerTitleView.setText("Set Verifit Target Output values corresponding to 65dB Speech for Band-" + String.valueOf(bandIdx + 1));
        final NumberPicker numberPicker = (NumberPicker) dialog.findViewById(R.id.number_picker);
        numberPicker.setMinValue(0);
        numberPicker.setMaxValue(outputHalfOctaveRange.length - 1);
        numberPicker.setDisplayedValues(outputHalfOctaveRange);
        int prevVal = Integer.parseInt(((Button) callingView).getText().toString());
        numberPicker.setValue(prevVal);
        numberPicker.setWrapSelectorWheel(true);

        Button okButton = (Button) dialog.findViewById(R.id.dialog_ok_btn);
        okButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String valStr = outputHalfOctaveRange[numberPicker.getValue()];
                ((Button) callingView).setText(valStr);
                outputHalfOctave[bandIdx] = Integer.parseInt(valStr);
                //OspInterface.getInstance().setG80(g80[bandIdx], bandIdx);
                dialog.dismiss();
            }
        });

        Button cancelButton = (Button) dialog.findViewById(R.id.dialog_cancel_btn);
        cancelButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                dialog.dismiss();
            }
        });

        dialog.show();
    }



    private void updateCompRatioG65(int bandIdx) {
        float slope = (g80[bandIdx] - g50[bandIdx]) / 30.0f;
        compRatio[bandIdx] = Float.parseFloat(String.format("%.2f", 1 / (1+slope)));
        OspInterface.getInstance().setCompRatio(compRatio[bandIdx], bandIdx);
        crButtons[bandIdx].setText(String.valueOf(compRatio[bandIdx]));
        g65[bandIdx] = (int) (g50[bandIdx] + slope*(65-50));
        OspInterface.getInstance().setG65(g65[bandIdx], bandIdx);
        g65Buttons[bandIdx].setText(String.valueOf(g65[bandIdx]));
    }

    private void updateG50G80(int bandIdx) {
        float slope = (1-compRatio[bandIdx]) / compRatio[bandIdx];
        g50[bandIdx] = (int) (g65[bandIdx] - slope*(65-50));
        g80[bandIdx] = (int) (g65[bandIdx] + slope*(80-65));
        OspInterface.getInstance().setG50(g50[bandIdx], bandIdx);
        g50Buttons[bandIdx].setText(String.valueOf(g50[bandIdx]));
        OspInterface.getInstance().setG80(g80[bandIdx], bandIdx);
        g80Buttons[bandIdx].setText(String.valueOf(g80[bandIdx]));
    }

    private void sendMessage() {
        sendParams();
        logActivity();
    }

    private void sendParams() {
        if (OspInterface.getInstance().isConnected()) {
            Thread thread = new Thread(new Runnable() {

                @Override
                public void run() {
                    try {
                        OspInterface.getInstance().sendParams();
                    } catch (Exception e) {
                        e.printStackTrace();
                    }
                }
            });
            thread.start();
        } else {
            Toast.makeText(getActivity(), "Not Connected", Toast.LENGTH_SHORT).show();
        }
    }

    private void logActivity() {
        if (OspInterface.getInstance().isConnected()) {
            Thread thread = new Thread(new Runnable() {
                @Override
                public void run() {
                    try {
                        String str = String.format("%s|%s|%s|%s|%d|%d|%d|%s|%s|%s|%s|%s|%s|%s|%s",
                                DateFormat.getDateTimeInstance().format(new Date()),
                                OspInterface.getInstance().getPageName(),
                                OspInterface.getInstance().getButtonName(),
                                Arrays.toString(OspInterface.getInstance().getCrispnessMultipliers()),
                                OspInterface.getInstance().getFullness(),
                                OspInterface.getInstance().getVolume(),
                                OspInterface.getInstance().getCrispness(),
                                Arrays.toString(OspInterface.getInstance().getCompRatio()),
                                Arrays.toString(OspInterface.getInstance().getG50()),
                                Arrays.toString(OspInterface.getInstance().getG65()),
                                Arrays.toString(OspInterface.getInstance().getG80()),
                                Arrays.toString(OspInterface.getInstance().getKneeLow()),
                                Arrays.toString(OspInterface.getInstance().getMPOLimit()),
                                Arrays.toString(OspInterface.getInstance().getAttackTime()),
                                Arrays.toString(OspInterface.getInstance().getReleaseTime())
                        );
                        Log.i("LogActivity", str.length() +" "+str);
                        OspInterface.getInstance().logUserActivity(str);
                    } catch (Exception e) {
                        e.printStackTrace();
                    }
                }
            });
            thread.start();
        } else {
            Toast.makeText(getActivity(), "Not Connected", Toast.LENGTH_SHORT).show();
        }
    }

}
