package edu.ucsd.hearingaid.osp_r01;

import android.app.Dialog;
import android.content.Context;
import android.content.DialogInterface;
import android.os.Bundle;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.NumberPicker;
import android.widget.RadioGroup;
import android.widget.TextView;

import java.util.Arrays;

/**
 * A login screen
 */
public class SettingsActivity extends AppCompatActivity implements View.OnClickListener {


    private final int NUM_BANDS = 6;


    private String[] gainValRange, compRatioValRange, kneeLowRange, mpoRange, attackRange, releaseRange;
    private String[] LStepRange, VStepRange, HStepRange, LLevelRange, VLevelRange, HLevelRange;
    private String[] multipliersRange;

    private Button LStepButton, VStepButton, HStepButton, LLevelButton, VLevelButton, HLevelButton, LValButton, VValButton, HValButton;

    private int[] compRatioIdx = new int[NUM_BANDS];
    private int fullness, volume, crispness;
    private static final int[] crButtonIDS = {R.id.settings_cr_1, R.id.settings_cr_2, R.id.settings_cr_3,
            R.id.settings_cr_4, R.id.settings_cr_5, R.id.settings_cr_6};
    private Button[] crButtons = new Button[crButtonIDS.length];
    private static final int[] g50ButtonIDS = {R.id.settings_g50_1, R.id.settings_g50_2, R.id.settings_g50_3,
            R.id.settings_g50_4, R.id.settings_g50_5, R.id.settings_g50_6};
    private Button[] g50Buttons = new Button[g50ButtonIDS.length];
    private static final int[] g65ButtonIDS = {R.id.settings_g65_1, R.id.settings_g65_2, R.id.settings_g65_3,
            R.id.settings_g65_4, R.id.settings_g65_5, R.id.settings_g65_6};
    private Button[] g65Buttons = new Button[g65ButtonIDS.length];
    private static final int[] g80ButtonIDS = {R.id.settings_g80_1, R.id.settings_g80_2, R.id.settings_g80_3,
            R.id.settings_g80_4, R.id.settings_g80_5, R.id.settings_g80_6};
    private Button[] g80Buttons = new Button[g80ButtonIDS.length];

    private static final int[] kneeLowButtonIDS = {R.id.settings_knee_low_1, R.id.settings_knee_low_2, R.id.settings_knee_low_3,
            R.id.settings_knee_low_4, R.id.settings_knee_low_5, R.id.settings_knee_low_6};
    private Button[] kneeLowButtons = new Button[kneeLowButtonIDS.length];
    private static final int[] MPOButtonIDS = {R.id.settings_mpo_1, R.id.settings_mpo_2, R.id.settings_mpo_3,
            R.id.settings_mpo_4, R.id.settings_mpo_5, R.id.settings_mpo_6};
    private Button[] MPOButton = new Button[MPOButtonIDS.length];
    private static final int[] attackButtonIDS = {R.id.settings_attack_1, R.id.settings_attack_2, R.id.settings_attack_3,
            R.id.settings_attack_4, R.id.settings_attack_5, R.id.settings_attack_6};
    private Button[] attackButtons = new Button[attackButtonIDS.length];
    private static final int[] releaseButtonIDS = {R.id.settings_release_1, R.id.settings_release_2, R.id.settings_release_3,
            R.id.settings_release_4, R.id.settings_release_5, R.id.settings_release_6};
    private Button[] releaseButtons = new Button[releaseButtonIDS.length];
    private static final int[] multiplierButtonIds = {R.id.settings_m1k_btn, R.id.settings_m2k_btn,
            R.id.settings_m4k_btn, R.id.settings_m8k_btn};
    private Button[] multiplierButtons = new Button[multiplierButtonIds.length];
    private static final int[] freqButtonIds = {R.id.settings_f1, R.id.settings_f2,
            R.id.settings_f3, R.id.settings_f4, R.id.settings_f5, R.id.settings_f6};
    private TextView[] freqButtons = new TextView[freqButtonIds.length];

    private RadioGroup mControlRadioGrp, mFilterRadioGrp;
    private int LStep, VStep, HStep, LStepIdx, VStepIdx, HStepIdx;
    private int LLevel, VLevel, HLevel;
    private int maxVolume, maxFullness, maxCrispness;

    private int[] filterCFreq;
    private int[] crispnessMultipliers;
    private float[] compRatio;
    private int[] g50;
    private int[] g65;
    private int[] g80;
    private int[] kneeLow;
    private int[] mpoLimits;
    private int[] attackTime;
    private int[] releaseTime;
    final Context context = this;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_settings);

        gainValRange = range(0, 100);
        kneeLowRange = range(0, 50);
        mpoRange = range(0, 200);
        attackRange = range(0, 200);
        releaseRange = range(0, 200);
        compRatioValRange = getResources().getStringArray(R.array.comp_ratio_vals);
        LStepRange = getResources().getStringArray(R.array.l_step_vals);
        VStepRange = getResources().getStringArray(R.array.v_step_vals);
        HStepRange = getResources().getStringArray(R.array.h_step_vals);

        multipliersRange = range(0, 5);

        LStepButton = (Button) findViewById(R.id.settings_l_step_btn);
        LStepButton.setOnClickListener(this);
        VStepButton = (Button) findViewById(R.id.settings_v_step_btn);
        VStepButton.setOnClickListener(this);
        HStepButton = (Button) findViewById(R.id.settings_h_step_btn);
        HStepButton.setOnClickListener(this);

        LLevelButton = (Button) findViewById(R.id.settings_l_level_btn);
        LLevelButton.setOnClickListener(this);
        VLevelButton = (Button) findViewById(R.id.settings_v_level_btn);
        VLevelButton.setOnClickListener(this);
        HLevelButton = (Button) findViewById(R.id.settings_h_level_btn);
        HLevelButton.setOnClickListener(this);

        LValButton = (Button) findViewById(R.id.settings_l_val_btn);
        LValButton.setOnClickListener(this);
        LValButton.setEnabled(false);
        VValButton = (Button) findViewById(R.id.settings_v_val_btn);
        VValButton.setOnClickListener(this);
        VValButton.setEnabled(false);
        HValButton = (Button) findViewById(R.id.settings_h_val_btn);
        HValButton.setOnClickListener(this);
        HValButton.setEnabled(false);

        for (int i = 0; i < multiplierButtonIds.length; i++) {
            final int b = i;
            multiplierButtons[b] = (Button) findViewById(multiplierButtonIds[b]);
            multiplierButtons[b].setOnClickListener(this);
        }
        for (int i = 0; i < NUM_BANDS; i++) {
            final int b = i;
            crButtons[b] = (Button) findViewById(crButtonIDS[b]);
            crButtons[b].setOnClickListener(this);
            g50Buttons[b] = (Button) findViewById(g50ButtonIDS[b]);
            g50Buttons[b].setOnClickListener(this);
            g65Buttons[b] = (Button) findViewById(g65ButtonIDS[b]);
            g65Buttons[b].setOnClickListener(this);
            g80Buttons[b] = (Button) findViewById(g80ButtonIDS[b]);
            g80Buttons[b].setOnClickListener(this);
            kneeLowButtons[b] = (Button) findViewById(kneeLowButtonIDS[b]);
            kneeLowButtons[b].setOnClickListener(this);
            MPOButton[b] = (Button) findViewById(MPOButtonIDS[b]);
            MPOButton[b].setOnClickListener(this);
            attackButtons[b] = (Button) findViewById(attackButtonIDS[b]);
            attackButtons[b].setOnClickListener(this);
            releaseButtons[b] = (Button) findViewById(releaseButtonIDS[b]);
            releaseButtons[b].setOnClickListener(this);
            freqButtons[b] = (TextView) findViewById(freqButtonIds[b]);
        }


        mControlRadioGrp = (RadioGroup) findViewById(R.id.settings_control_radio_group);
        mControlRadioGrp.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
            public void onCheckedChanged(RadioGroup group, int checkedId) {
                // checkedId is the RadioButton selected
                switch (checkedId) {
                    case R.id.settings_radio_comp:
                        disableG50G80Rows();
                        enableCRG65Rows();
                        break;
                    case R.id.settings_radio_g50:
                        disableCRG65Rows();
                        enableG50G80Rows();
                        break;
                }
            }
        });

        mFilterRadioGrp = (RadioGroup) findViewById(R.id.settings_filters_radio_group);
        mFilterRadioGrp.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
            public void onCheckedChanged(RadioGroup group, int checkedId) {
                // checkedId is the RadioButton selected
                switch (checkedId) {
                    case R.id.settings_radio_boothroyd:
                        filterCFreq = getResources().getIntArray(R.array.boothroyd_cfreq);
                        break;
                    case R.id.settings_radio_kates:
                        filterCFreq = getResources().getIntArray(R.array.kates_cfreq);
                        break;
                }
                OspInterface.getInstance().setCFreq(filterCFreq);
                for (int i = 0; i < freqButtonIds.length; i++) {
                    freqButtons[i].setText(String.valueOf(filterCFreq[i]));
                }
            }
        });

        Button resetButton = (Button) findViewById(R.id.settings_reset_btn);
        resetButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                AlertDialog.Builder builder = new AlertDialog.Builder(context);
                builder.setMessage(R.string.sure_about_resetting)
                        .setPositiveButton(R.string.yes, new DialogInterface.OnClickListener() {
                            public void onClick(DialogInterface dialog, int id) {
                                OspInterface.getInstance().setToDefaultParams();
                                setParams();
                            }
                        })
                        .setNegativeButton(R.string.no, new DialogInterface.OnClickListener() {
                            public void onClick(DialogInterface dialog, int id) {

                            }
                        });
                builder.show();
            }
        });

        Button saveButton = (Button) findViewById(R.id.settings_save_btn);
        saveButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });
        setParams();
    }

    private void setParams() {
        LStep = OspInterface.getInstance().getFullnessStep();
        VStep = OspInterface.getInstance().getVolumeStep();
        HStep = OspInterface.getInstance().getCrispnessStep();
        LLevel = OspInterface.getInstance().getFullnessLevel();
        VLevel = OspInterface.getInstance().getVolumeLevel();
        HLevel = OspInterface.getInstance().getCrispnessLevel();
        fullness = OspInterface.getInstance().getFullness();
        volume = OspInterface.getInstance().getVolume();
        crispness = OspInterface.getInstance().getCrispness();
        compRatio = OspInterface.getInstance().getCompRatio();
        g50 = OspInterface.getInstance().getG50();
        g65 = OspInterface.getInstance().getG65();
        g80 = OspInterface.getInstance().getG80();
        kneeLow = OspInterface.getInstance().getKneeLow();
        mpoLimits = OspInterface.getInstance().getMPOLimit();
        attackTime = OspInterface.getInstance().getAttackTime();
        releaseTime = OspInterface.getInstance().getReleaseTime();
        maxFullness = OspInterface.getInstance().getMaxFullness();
        maxVolume = OspInterface.getInstance().getMaxVolume();
        maxCrispness = OspInterface.getInstance().getMaxCrispness();
        crispnessMultipliers = OspInterface.getInstance().getCrispnessMultipliers();

        LStepIdx = Arrays.asList(LStepRange).indexOf(String.valueOf(LStep));
        VStepIdx = Arrays.asList(VStepRange).indexOf(String.valueOf(VStep));
        HStepIdx = Arrays.asList(HStepRange).indexOf(String.valueOf(HStep));

        LLevelRange = range(0, maxFullness / LStep + 1);
        VLevelRange = range(0, maxVolume / VStep + 1);
        HLevelRange = range(0, maxCrispness / HStep + 1);

        LStepButton.setText(String.valueOf(LStep));
        VStepButton.setText(String.valueOf(VStep));
        HStepButton.setText(String.valueOf(HStep));

        LLevelButton.setText(String.valueOf(LLevel));
        VLevelButton.setText(String.valueOf(VLevel));
        HLevelButton.setText(String.valueOf(HLevel));

        LValButton.setText(String.valueOf(fullness));
        VValButton.setText(String.valueOf(volume));
        HValButton.setText(String.valueOf(crispness));

        mControlRadioGrp.check(R.id.settings_radio_g50);
        mFilterRadioGrp.check(R.id.settings_radio_boothroyd);
        filterCFreq = getResources().getIntArray(R.array.boothroyd_cfreq);
        OspInterface.getInstance().setCFreq(filterCFreq);

        for (int i = 0; i < multiplierButtonIds.length; i++) {
            multiplierButtons[i].setText(String.valueOf(crispnessMultipliers[i]));
        }
        for (int i = 0; i < NUM_BANDS; i++) {
            crButtons[i].setText(String.valueOf(compRatio[i]));
            g50Buttons[i].setText(String.valueOf(g50[i]));
            g65Buttons[i].setText(String.valueOf(g65[i]));
            g80Buttons[i].setText(String.valueOf(g80[i]));
            kneeLowButtons[i].setText(String.valueOf(kneeLow[i]));
            MPOButton[i].setText(String.valueOf(mpoLimits[i]));
            attackButtons[i].setText(String.valueOf(attackTime[i]));
            releaseButtons[i].setText(String.valueOf(releaseTime[i]));
            freqButtons[i].setText(String.valueOf(filterCFreq[i]));
        }

        disableCRG65Rows();
        enableG50G80Rows();
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


    public static MHAFragment newInstance() {
        MHAFragment f = new MHAFragment();
        return f;
    }


    private String[] range(int start, int stop) {
        int[] result = new int[stop - start];

        for (int i = 0; i < stop - start; i++)
            result[i] = start + i;

        String[] a = Arrays.toString(result).split("[\\[\\]]")[1].split(", ");
        return a;
    }


    @Override
    public void onClick(View v) {
        switch (v.getId()) {
            case R.id.settings_l_step_btn:
                showLVHStepDialog(LStepRange, v, "Select LF Cut Step Size");
                break;
            case R.id.settings_v_step_btn:
                showLVHStepDialog(VStepRange, v, "Select Volume Step Size");
                break;
            case R.id.settings_h_step_btn:
                showLVHStepDialog(HStepRange, v, "Select HF Boost Step Size");
                break;
            case R.id.settings_l_level_btn:
                showLVHLevelDialog(LLevelRange, v, "Select LF Cut Level");
                break;
            case R.id.settings_v_level_btn:
                showLVHLevelDialog(VLevelRange, v, "Select Volume Level");
                break;
            case R.id.settings_h_level_btn:
                showLVHLevelDialog(HLevelRange, v, "Select HF Boost Level");
                break;
            case R.id.settings_m1k_btn:
                showMultipliersDialog(v, 0);
                break;
            case R.id.settings_m2k_btn:
                showMultipliersDialog(v, 1);
                break;
            case R.id.settings_m4k_btn:
                showMultipliersDialog(v, 2);
                break;
            case R.id.settings_m8k_btn:
                showMultipliersDialog(v, 3);
                break;

            case R.id.settings_cr_1:
                showCompRatioDialog(v, 0);
                break;
            case R.id.settings_cr_2:
                showCompRatioDialog(v, 1);
                break;
            case R.id.settings_cr_3:
                showCompRatioDialog(v, 2);
                break;
            case R.id.settings_cr_4:
                showCompRatioDialog(v, 3);
                break;
            case R.id.settings_cr_5:
                showCompRatioDialog(v, 4);
                break;
            case R.id.settings_cr_6:
                showCompRatioDialog(v, 5);
                break;
            case R.id.settings_g50_1:
                showGain50Dialog(v, 0);
                break;
            case R.id.settings_g50_2:
                showGain50Dialog(v, 1);
                break;
            case R.id.settings_g50_3:
                showGain50Dialog(v, 2);
                break;
            case R.id.settings_g50_4:
                showGain50Dialog(v, 3);
                break;
            case R.id.settings_g50_5:
                showGain50Dialog(v, 4);
                break;
            case R.id.settings_g50_6:
                showGain50Dialog(v, 5);
                break;
            case R.id.settings_g65_1:
                showGain65Dialog(v, 0);
                break;
            case R.id.settings_g65_2:
                showGain65Dialog(v, 1);
                break;
            case R.id.settings_g65_3:
                showGain65Dialog(v, 2);
                break;
            case R.id.settings_g65_4:
                showGain65Dialog(v, 3);
                break;
            case R.id.settings_g65_5:
                showGain65Dialog(v, 4);
                break;
            case R.id.settings_g65_6:
                showGain65Dialog(v, 5);
                break;
            case R.id.settings_g80_1:
                showGain80Dialog(v, 0);
                break;
            case R.id.settings_g80_2:
                showGain80Dialog(v, 1);
                break;
            case R.id.settings_g80_3:
                showGain80Dialog(v, 2);
                break;
            case R.id.settings_g80_4:
                showGain80Dialog(v, 3);
                break;
            case R.id.settings_g80_5:
                showGain80Dialog(v, 4);
                break;
            case R.id.settings_g80_6:
                showGain80Dialog(v, 5);
                break;
            case R.id.settings_knee_low_1:
                showKneeLowDialog(v, 0);
                break;
            case R.id.settings_knee_low_2:
                showKneeLowDialog(v, 1);
                break;
            case R.id.settings_knee_low_3:
                showKneeLowDialog(v, 2);
                break;
            case R.id.settings_knee_low_4:
                showKneeLowDialog(v, 3);
                break;
            case R.id.settings_knee_low_5:
                showKneeLowDialog(v, 4);
                break;
            case R.id.settings_knee_low_6:
                showKneeLowDialog(v, 5);
                break;

            case R.id.settings_mpo_1:
                showKneeHighDialog(v, 0);
                break;
            case R.id.settings_mpo_2:
                showKneeHighDialog(v, 1);
                break;
            case R.id.settings_mpo_3:
                showKneeHighDialog(v, 2);
                break;
            case R.id.settings_mpo_4:
                showKneeHighDialog(v, 3);
                break;
            case R.id.settings_mpo_5:
                showKneeHighDialog(v, 4);
                break;
            case R.id.settings_mpo_6:
                showKneeHighDialog(v, 5);
                break;

            case R.id.settings_attack_1:
                showAttackDialog(v, 0);
                break;
            case R.id.settings_attack_2:
                showAttackDialog(v, 1);
                break;
            case R.id.settings_attack_3:
                showAttackDialog(v, 2);
                break;
            case R.id.settings_attack_4:
                showAttackDialog(v, 3);
                break;
            case R.id.settings_attack_5:
                showAttackDialog(v, 4);
                break;
            case R.id.settings_attack_6:
                showAttackDialog(v, 5);
                break;

            case R.id.settings_release_1:
                showReleaseDialog(v, 0);
                break;
            case R.id.settings_release_2:
                showReleaseDialog(v, 1);
                break;
            case R.id.settings_release_3:
                showReleaseDialog(v, 2);
                break;
            case R.id.settings_release_4:
                showReleaseDialog(v, 3);
                break;
            case R.id.settings_release_5:
                showReleaseDialog(v, 4);
                break;
            case R.id.settings_release_6:
                showReleaseDialog(v, 5);
                break;

            default:
                Log.i("OnClick", ((Button) v).getText().toString());
        }
    }

    private void showLVHStepDialog(final String[] values, final View callingView, String pickerTitle) {
        final Dialog dialog = new Dialog(this);
        dialog.setContentView(R.layout.number_picker_dialog);
        final TextView numberPickerTitleView = (TextView) dialog.findViewById(R.id.number_picker_title);
        numberPickerTitleView.setText(pickerTitle);
        final NumberPicker numberPicker = (NumberPicker) dialog.findViewById(R.id.number_picker);
        numberPicker.setMinValue(0);
        numberPicker.setDisplayedValues(values);
        numberPicker.setMaxValue(values.length - 1);
        switch (callingView.getId()) {
            case R.id.settings_l_step_btn:
                numberPicker.setValue(LStepIdx);
                break;
            case R.id.settings_v_step_btn:
                numberPicker.setValue(VStepIdx);
                break;
            case R.id.settings_h_step_btn:
                numberPicker.setValue(HStepIdx);
                break;
        }
        numberPicker.setWrapSelectorWheel(true);

        Button okButton = (Button) dialog.findViewById(R.id.dialog_ok_btn);
        okButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String valStr = values[numberPicker.getValue()];
                ((Button) callingView).setText(valStr);
                //TODO: Do math for gain calculations and set gains
                switch (callingView.getId()) {
                    case R.id.settings_l_step_btn:
                        LStep = Integer.parseInt(valStr);
                        LStepIdx = numberPicker.getValue();
                        OspInterface.getInstance().setFullnessStep(LStep);
                        LLevelRange = range(0, maxFullness / LStep + 1);
                        if (LLevel > (maxFullness/LStep) ) {
                            LLevel = maxFullness / LStep;
                            LLevelButton.setText(String.valueOf(LLevel));
                            OspInterface.getInstance().setFullnessLevel(LLevel);
                        }
                        fullness = LStep * LLevel;
                        OspInterface.getInstance().setFullness(fullness);
                        LValButton.setText(String.valueOf(fullness));
                        fullnessChanged();
                        break;
                    case R.id.settings_v_step_btn:
                        VStep = Integer.parseInt(valStr);
                        VStepIdx = numberPicker.getValue();
                        OspInterface.getInstance().setVolumeStep(VStep);
                        VLevelRange = range(0, maxVolume / VStep + 1);
                        if (VLevel > (maxVolume/VStep) ) {
                            VLevel = maxVolume / VStep;
                            VLevelButton.setText(String.valueOf(VLevel));
                            OspInterface.getInstance().setVolumeLevel(VLevel);
                        }
                        volume = VStep * VLevel;
                        OspInterface.getInstance().setVolume(volume);
                        VValButton.setText(String.valueOf(volume));
                        volumeChanged();
                        break;
                    case R.id.settings_h_step_btn:
                        HStep = Integer.parseInt(valStr);
                        HStepIdx = numberPicker.getValue();
                        OspInterface.getInstance().setCrispnessStep(HStep);
                        HLevelRange = range(0, maxCrispness / HStep + 1);
                        if (HLevel > (maxCrispness / HStep ) ) {
                            HLevel = maxCrispness / HStep;
                            OspInterface.getInstance().setCrispnessLevel(HLevel);
                            HLevelButton.setText(String.valueOf(HLevel));
                        }
                        crispness = HStep * HLevel;
                        OspInterface.getInstance().setCrispness(crispness);
                        HValButton.setText(String.valueOf(crispness));
                        crispnessChanged();
                        break;
                }
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

    private void showLVHLevelDialog(final String[] values, final View callingView, String pickerTitle) {
        final Dialog dialog = new Dialog(this);
        dialog.setContentView(R.layout.number_picker_dialog);
        final TextView numberPickerTitleView = (TextView) dialog.findViewById(R.id.number_picker_title);
        numberPickerTitleView.setText(pickerTitle);
        final NumberPicker numberPicker = (NumberPicker) dialog.findViewById(R.id.number_picker);
        numberPicker.setMinValue(0);
        numberPicker.setMaxValue(values.length - 1);
        numberPicker.setDisplayedValues(values);
        int prevVal = Integer.parseInt(((Button) callingView).getText().toString());
        numberPicker.setValue(prevVal);
        numberPicker.setWrapSelectorWheel(true);

        Button okButton = (Button) dialog.findViewById(R.id.dialog_ok_btn);
        okButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String valStr = values[numberPicker.getValue()];
                ((Button) callingView).setText(valStr);
                //TODO: Do math for gain calculations and set gains
                switch (callingView.getId()) {
                    case R.id.settings_l_level_btn:
                        LLevel = Integer.parseInt(valStr);
                        OspInterface.getInstance().setFullnessLevel(LLevel);
                        fullness = LStep * LLevel;
                        OspInterface.getInstance().setFullness(fullness);
                        LValButton.setText(String.valueOf(fullness));
                        fullnessChanged();
                        break;
                    case R.id.settings_v_level_btn:
                        VLevel = Integer.parseInt(valStr);
                        OspInterface.getInstance().setVolumeLevel(VLevel);
                        volume = VStep * VLevel;
                        OspInterface.getInstance().setVolume(volume);
                        VValButton.setText(String.valueOf(volume));
                        volumeChanged();
                        break;
                    case R.id.settings_h_level_btn:
                        HLevel = Integer.parseInt(valStr);
                        OspInterface.getInstance().setCrispnessLevel(HLevel);
                        crispness = HStep * HLevel;
                        OspInterface.getInstance().setCrispness(crispness);
                        HValButton.setText(String.valueOf(crispness));
                        crispnessChanged();
                        break;
                }
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

    private void showMultipliersDialog(final View callingView, final int idx) {
        final Dialog dialog = new Dialog(this);
        dialog.setContentView(R.layout.number_picker_dialog);
        final TextView numberPickerTitleView = (TextView) dialog.findViewById(R.id.number_picker_title);
        numberPickerTitleView.setText("Select Crispness Multiplier for Band-" + String.valueOf(idx + 3));
        final NumberPicker numberPicker = (NumberPicker) dialog.findViewById(R.id.number_picker);
        numberPicker.setMinValue(0);
        numberPicker.setMaxValue(multipliersRange.length - 1);
        numberPicker.setDisplayedValues(multipliersRange);
        int prevVal = Integer.parseInt(((Button) callingView).getText().toString());
        numberPicker.setValue(prevVal);
        numberPicker.setWrapSelectorWheel(true);

        Button okButton = (Button) dialog.findViewById(R.id.dialog_ok_btn);
        okButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String valStr = multipliersRange[numberPicker.getValue()];
                ((Button) callingView).setText(valStr);
                crispnessMultipliers[idx] = Integer.parseInt(valStr);
                OspInterface.getInstance().setG50(crispnessMultipliers[idx], idx);
                crispnessChanged();
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

    private void showCompRatioDialog(final View callingView, final int bandIdx) {
        final Dialog dialog = new Dialog(this);
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
        final Dialog dialog = new Dialog(this);
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
        final Dialog dialog = new Dialog(this);
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
        final Dialog dialog = new Dialog(this);
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

    private void showKneeLowDialog(final View callingView, final int bandIdx) {
        final Dialog dialog = new Dialog(this);
        dialog.setContentView(R.layout.number_picker_dialog);
        final TextView numberPickerTitleView = (TextView) dialog.findViewById(R.id.number_picker_title);
        numberPickerTitleView.setText("Set Knee low for Band-" + String.valueOf(bandIdx + 1));
        final NumberPicker numberPicker = (NumberPicker) dialog.findViewById(R.id.number_picker);
        numberPicker.setMinValue(0);
        numberPicker.setMaxValue(kneeLowRange.length - 1);
        numberPicker.setDisplayedValues(kneeLowRange);
        int prevVal = Integer.parseInt(((Button) callingView).getText().toString());
        numberPicker.setValue(prevVal);
        numberPicker.setWrapSelectorWheel(true);

        Button okButton = (Button) dialog.findViewById(R.id.dialog_ok_btn);
        okButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String valStr = kneeLowRange[numberPicker.getValue()];
                ((Button) callingView).setText(valStr);
                kneeLow[bandIdx] = Integer.parseInt(valStr);
                OspInterface.getInstance().setKneeLow(kneeLow[bandIdx], bandIdx);

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

    private void showKneeHighDialog(final View callingView, final int bandIdx) {
        final Dialog dialog = new Dialog(this);
        dialog.setContentView(R.layout.number_picker_dialog);
        final TextView numberPickerTitleView = (TextView) dialog.findViewById(R.id.number_picker_title);
        numberPickerTitleView.setText("Set MPO Limit for Band-" + String.valueOf(bandIdx + 1));
        final NumberPicker numberPicker = (NumberPicker) dialog.findViewById(R.id.number_picker);
        numberPicker.setMinValue(0);
        numberPicker.setMaxValue(mpoRange.length - 1);
        numberPicker.setDisplayedValues(mpoRange);
        int prevVal = Integer.parseInt(((Button) callingView).getText().toString());
        numberPicker.setValue(prevVal);
        numberPicker.setWrapSelectorWheel(true);

        Button okButton = (Button) dialog.findViewById(R.id.dialog_ok_btn);
        okButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String valStr = mpoRange[numberPicker.getValue()];
                ((Button) callingView).setText(valStr);
                mpoLimits[bandIdx] = Integer.parseInt(valStr);
                OspInterface.getInstance().setMPOLimit(mpoLimits[bandIdx], bandIdx);

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

    private void showAttackDialog(final View callingView, final int bandIdx) {
        final Dialog dialog = new Dialog(this);
        dialog.setContentView(R.layout.number_picker_dialog);
        final TextView numberPickerTitleView = (TextView) dialog.findViewById(R.id.number_picker_title);
        numberPickerTitleView.setText("Set Attack time for Band-" + String.valueOf(bandIdx + 1));
        final NumberPicker numberPicker = (NumberPicker) dialog.findViewById(R.id.number_picker);
        numberPicker.setMinValue(0);
        numberPicker.setMaxValue(attackRange.length - 1);
        numberPicker.setDisplayedValues(attackRange);
        int prevVal = Integer.parseInt(((Button) callingView).getText().toString());
        numberPicker.setValue(prevVal);
        numberPicker.setWrapSelectorWheel(true);

        Button okButton = (Button) dialog.findViewById(R.id.dialog_ok_btn);
        okButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String valStr = attackRange[numberPicker.getValue()];
                ((Button) callingView).setText(valStr);
                attackTime[bandIdx] = Integer.parseInt(valStr);
                OspInterface.getInstance().setAttackTime(attackTime[bandIdx], bandIdx);

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

    private void showReleaseDialog(final View callingView, final int bandIdx) {
        final Dialog dialog = new Dialog(this);
        dialog.setContentView(R.layout.number_picker_dialog);
        final TextView numberPickerTitleView = (TextView) dialog.findViewById(R.id.number_picker_title);
        numberPickerTitleView.setText("Set Release Time for Band-" + String.valueOf(bandIdx + 1));
        final NumberPicker numberPicker = (NumberPicker) dialog.findViewById(R.id.number_picker);
        numberPicker.setMinValue(0);
        numberPicker.setMaxValue(releaseRange.length - 1);
        numberPicker.setDisplayedValues(releaseRange);
        int prevVal = Integer.parseInt(((Button) callingView).getText().toString());
        numberPicker.setValue(prevVal);
        numberPicker.setWrapSelectorWheel(true);

        Button okButton = (Button) dialog.findViewById(R.id.dialog_ok_btn);
        okButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String valStr = releaseRange[numberPicker.getValue()];
                ((Button) callingView).setText(valStr);
                releaseTime[bandIdx] = Integer.parseInt(valStr);
                OspInterface.getInstance().setReleaseTime(releaseTime[bandIdx], bandIdx);

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
        compRatio[bandIdx] = Float.parseFloat(String.format("%.2f", 1 / (1 + slope)));
        OspInterface.getInstance().setCompRatio(compRatio[bandIdx], bandIdx);
        crButtons[bandIdx].setText(String.valueOf(compRatio[bandIdx]));
        g65[bandIdx] = (int) (g50[bandIdx] + slope * (65 - 50));
        OspInterface.getInstance().setG65(g65[bandIdx], bandIdx);
        g65Buttons[bandIdx].setText(String.valueOf(g65[bandIdx]));
    }

    private void updateG50G80(int bandIdx) {
        float slope = (1 - compRatio[bandIdx]) / compRatio[bandIdx];
        g50[bandIdx] = (int) (g65[bandIdx] - slope * (65 - 50));
        g80[bandIdx] = (int) (g65[bandIdx] + slope * (80 - 65));
        OspInterface.getInstance().setG50(g50[bandIdx], bandIdx);
        g50Buttons[bandIdx].setText(String.valueOf(g50[bandIdx]));
        OspInterface.getInstance().setG80(g80[bandIdx], bandIdx);
        g80Buttons[bandIdx].setText(String.valueOf(g80[bandIdx]));
    }

    private void crispnessChanged() {
        for(int i=2; i<NUM_BANDS; i++) {
            g65[i] = volume + crispness * crispnessMultipliers[i-2];
        }
        updateGains();
    }

    private void fullnessChanged() {
        g65[0] = volume - fullness;
        updateGains();
    }

    private void volumeChanged() {
        g65[0] = volume - fullness;
        g65[1] = volume;
        for(int i=2; i<NUM_BANDS; i++) {
            g65[i] = volume + crispness * crispnessMultipliers[i-2];
        }
        updateGains();
    }

    private void updateGains() {
        for(int i=0; i<NUM_BANDS; i++) {
            float slope = (1-compRatio[i]) / compRatio[i];
            g50[i] = (int) (g65[i] - slope*(65-50));
            g80[i] = (int) (g65[i] + slope*(80-65));
        }
        commitG50();
        commitG65();
        commitG80();
    }

    private void commitG50() {
        for(int i=0; i<NUM_BANDS; i++) {
            g50Buttons[i].setText(String.valueOf(g50[i]));
            OspInterface.getInstance().setG50(g50[i], i);
        }
    }

    private void commitG65() {
        for(int i=0; i<NUM_BANDS; i++) {
            g65Buttons[i].setText(String.valueOf(g65[i]));
            OspInterface.getInstance().setG50(g65[i], i);
        }
    }

    private void commitG80() {
        for(int i=0; i<NUM_BANDS; i++) {
            g80Buttons[i].setText(String.valueOf(g80[i]));
            OspInterface.getInstance().setG50(g80[i], i);
        }
    }

}

