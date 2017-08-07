package edu.ucsd.hearingaid.osp_r01;

import java.nio.ByteBuffer;
import java.nio.ByteOrder;
import java.util.Arrays;

public class OspInterface {
    private final int NUM_BANDS = 6;
    private final byte REQUEST_UPDATE_VALUES = 0X01;
    private final byte REQUEST_GET_UNDERRUNS = 0X02;
    private final byte REQUEST_USER_ID = 0x03;
    private final byte REQUEST_USER_ACTION = 0x04;
    private final byte VERSION = 0x02;

    /* Interface structure */
    private int noOp;
    private int afc;
    private int feedback;
    private int rearMics;
    private int[] g50 = new int[NUM_BANDS];
    private int[] g80 = new int[NUM_BANDS];
    private int[] kneeLow = new int[NUM_BANDS];
    private int[] MPOLimit = new int[NUM_BANDS];
    private int[] attackTime = new int[NUM_BANDS];
    private int[] releaseTime = new int[NUM_BANDS];
    /* Interface structure */

    private int underruns;
    private TCPClient ospTcp;
    private int tcpPort = 8001;
    private String userInitals;
    private String researcherInitials;
    private String pageName;
    private String buttonName;

    private int volume;
    private int fullness;
    private int crispness;
    private int fullnessStep;
    private int volumeStep;
    private int crispnessStep;
    private int[] crispnessMultipliers = new int[NUM_BANDS-2];
    private float[] compRatio = new float[NUM_BANDS];
    private int[] g65 = new int[NUM_BANDS];
    private int[] cFreq = new int[NUM_BANDS];
    private int maxFullness, maxVolume, maxCrispness;
    private int fullnessLevel, volumeLevel, crispnessLevel;

    private boolean isConnected = false;

    public OspInterface() {
        noOp = 0;
        afc = 1;
        feedback = 0;
        rearMics = 1;
        underruns = 0;
        setToDefaultParams();
    }

    public void setNoOp(boolean noOp) {
        this.noOp = (noOp) ? 1 : 0;
    }

    public void setAfc(boolean afc) {
        this.afc = (afc) ? 1 : 0;
    }

    public void setFeedback(boolean fb) {
        this.feedback = (fb) ? 1 : 0;
    }

    public void setRearMics(boolean rm) {
        this.rearMics = (rm) ? 1 : 0;
    }

    public void setParams(int[] g50, int[] g80, int[] kneeLow, int[] kneeHigh, int[] attack, int[] release) {
        for (int i = 0; i < NUM_BANDS; i++) {
            this.g50[i] = g50[i];
            this.g80[i] = g80[i];
            this.MPOLimit[i] = kneeHigh[i];
            this.kneeLow[i] = kneeLow[i];
            this.attackTime[i] = attack[i];
            this.releaseTime[i] = release[i];
        }
    }

    public void setButtonName(String buttonName) {
        this.buttonName = buttonName;
    }

    public String getButtonName() {
        return buttonName;
    }

    public void setPageName(String pageName) {
        this.pageName = pageName;
    }

    public String getPageName() {
        return pageName;
    }

    public void setUserInitals(String userInitals) {
        this.userInitals = userInitals;
    }

    public String getUserInitals() {
        return userInitals;
    }

    public void setG50(int[] g50) {
        for (int i = 0; i < NUM_BANDS; i++) {
            this.g50[i] = g50[i];
        }
    }

    public void setG50(int g50, int bandIdx) {
        this.g50[bandIdx] = g50;
    }

    public int[] getG50() {
        return g50;
    }

    public void setG65(int[] g65) {
        for (int i = 0; i < NUM_BANDS; i++) {
            this.g65[i] = g65[i];
        }
    }

    public void setG65(int g65, int bandIdx) {
        this.g65[bandIdx] = g65;
    }

    public int[] getG65() {
        return g65;
    }

    public void setG80(int[] g80) {
        for (int i = 0; i < NUM_BANDS; i++) {
            this.g80[i] = g80[i];
        }
    }

    public void setG80(int g80, int bandIdx) {
        this.g80[bandIdx] = g80;
    }


    public int[] getG80() {
        return g80;
    }

    public void setCompRatio(float[] compRatio) {
        for (int i = 0; i < NUM_BANDS; i++) {
            this.compRatio[i] = compRatio[i];
        }
    }

    public void setCompRatio(float compRatio, int idx) {
        this.compRatio[idx] = compRatio;
    }

    public float[] getCompRatio() {
        return compRatio;
    }

    public void setReleaseTime(int[] releaseTime) {
        for (int i = 0; i < NUM_BANDS; i++) {
            this.releaseTime[i] = releaseTime[i];
        }
    }

    public void setReleaseTime(int releaseTime, int bandIdx) {
        this.releaseTime[bandIdx] = releaseTime;
    }

    public int[] getReleaseTime() {
        return releaseTime;
    }

    public void setAttackTime(int[] attackTime) {
        for (int i = 0; i < NUM_BANDS; i++) {
            this.attackTime[i] = attackTime[i];
        }
    }

    public void setAttackTime(int attackTime, int bandIdx) {
        this.attackTime[bandIdx] = attackTime;
    }

    public int[] getAttackTime() {
        return attackTime;
    }

    public void setMPOLimit(int[] MPOLimit) {
        for (int i = 0; i < NUM_BANDS; i++) {
            this.MPOLimit[i] = MPOLimit[i];
        }
    }

    public void setMPOLimit(int mpoLimit, int bandIdx) {
        this.MPOLimit[bandIdx] = mpoLimit;
    }

    public int[] getMPOLimit() {
        return MPOLimit;
    }

    public void setKneeLow(int[] kneeLow) {
        for (int i = 0; i < NUM_BANDS; i++) {
            this.kneeLow[i] = kneeLow[i];
        }
    }

    public void setKneeLow(int kneeLow, int bandIdx) {
        this.kneeLow[bandIdx] = kneeLow;
    }

    public int[] getKneeLow() {
        return kneeLow;
    }

    public void setFullness(int fullness) {
        this.fullness = fullness;
    }

    public int getFullness() {
        return fullness;
    }

    public void setCrispness(int crispness) {
        this.crispness = crispness;
    }

    public int getCrispness() {
        return crispness;
    }

    public void setVolume(int volume) {
        this.volume = volume;
    }

    public int getVolume() {
        return volume;
    }

    public void setVolumeStep(int volumeStep) {
        this.volumeStep = volumeStep;
    }

    public int getVolumeStep() {
        return volumeStep;
    }

    public void setFullnessStep(int fullnessStep) {
        this.fullnessStep = fullnessStep;
    }

    public int getFullnessStep() {
        return fullnessStep;
    }

    public void setCrispnessStep(int crispnessStep) {
        this.crispnessStep = crispnessStep;
    }

    public int getCrispnessStep() {
        return crispnessStep;
    }

    public void setCrispnessMultipliers(int[] crispnessMultipliers) {
        for (int i = 0; i < 4; i++) {
            this.crispnessMultipliers[i] = crispnessMultipliers[i];
        }
    }

    public void setCrispnessMultipliers(int multiplier, int idx) {
        this.crispnessMultipliers[idx] = multiplier;
    }

    public int[] getCrispnessMultipliers() {
        return crispnessMultipliers;
    }

    public int getFullnessLevel() {
        return fullnessLevel;
    }

    public void setFullnessLevel(int fullnessLevel) {
        this.fullnessLevel = fullnessLevel;
    }

    public int getVolumeLevel() {
        return volumeLevel;
    }

    public void setVolumeLevel(int volumeLevel) {
        this.volumeLevel = volumeLevel;
    }

    public int getCrispnessLevel() {
        return crispnessLevel;
    }

    public void setCrispnessLevel(int crispnessLevel) {
        this.crispnessLevel = crispnessLevel;
    }

    public int getMaxFullness() {
        return maxFullness;
    }

    public void setMaxFullness(int maxFullness) {
        this.maxFullness = maxFullness;
    }

    public int getMaxVolume() {
        return maxVolume;
    }

    public void setMaxVolume(int maxVolume) {
        this.maxVolume = maxVolume;
    }

    public int getMaxCrispness() {
        return maxCrispness;
    }

    public void setMaxCrispness(int maxCrispness) {
        this.maxCrispness = maxCrispness;
    }


    public void connect(String ipAddress, String user) throws OspInterfaceException {
        ospTcp = new TCPClient(tcpPort);
        ospTcp.connect(ipAddress);
        isConnected = ospTcp.isConnected();
        if(isConnected) {
            logUserId(user);
        }
    }

    public boolean isConnected() {
        return isConnected;
    }

    public void stopClient() {
        if(ospTcp != null)    {
            ospTcp.stopClient();
            isConnected = ospTcp.isConnected();
            ospTcp = null;
        }
    }

    public void sendParams() {
        ByteBuffer pkt = ByteBuffer.allocate(162);
        pkt.order(ByteOrder.LITTLE_ENDIAN);

        //pkt.put(REQUEST_UPDATE_VALUES);
        pkt.put(REQUEST_UPDATE_VALUES);
        pkt.put(VERSION);
        pkt.put(getReqUpdateBuffer());
        ospTcp.sendMessage(pkt.array());
    }

    public void logUserActivity(String activity) throws OspInterfaceException {
        if (activity.length() > Integer.MAX_VALUE) {
            throw new OspInterfaceException("Activity string is too long");
        }

        ByteBuffer pkt = ByteBuffer.allocate(activity.length() + 6);
        pkt.order(ByteOrder.LITTLE_ENDIAN);

        pkt.put(REQUEST_USER_ACTION);
        pkt.put(VERSION);
        pkt.putInt(activity.length());
        pkt.put(activity.getBytes());

        ospTcp.sendMessage(pkt.array());
    }

    public void logUserId(String user) throws OspInterfaceException {
        if (user.length() > Byte.MAX_VALUE) {
            throw new OspInterfaceException("Username is too long. Must be less than " + Byte.MAX_VALUE + " characters");
        }

        ByteBuffer pkt = ByteBuffer.allocate(user.length() + 6);
        pkt.order(ByteOrder.LITTLE_ENDIAN);

        pkt.put(REQUEST_USER_ID);
        pkt.put(VERSION);
        pkt.putInt(user.length());
        pkt.put(user.getBytes());

        ospTcp.sendMessage(pkt.array());
    }

    public void setGainsChoice(String in) {
        switch (in) {
            case "NH":
                Arrays.fill(g50, 0);
                Arrays.fill(g80, 0);
                break;
            case "N2":
                g50[0] = 2;
                g50[1] = 6;
                g50[2] = 17;
                g50[3] = 21;
                g50[4] = 20;
                g50[5] = 20;

                g80[0] = 1;
                g80[1] = 1;
                g80[2] = 4;
                g80[3] = 8;
                g80[4] = 14;
                g80[5] = 14;
                break;
            case "N4":
                g50[0] = 17;
                g50[1] = 24;
                g50[2] = 34;
                g50[3] = 36;
                g50[4] = 33;
                g50[5] = 33;

                g80[0] = 8;
                g80[1] = 12;
                g80[2] = 19;
                g80[3] = 25;
                g80[4] = 24;
                g80[5] = 24;
                break;
            case "S2":
                g50[0] = 2;
                g50[1] = 7;
                g50[2] = 19;
                g50[3] = 28;
                g50[4] = 33;
                g50[5] = 33;

                g80[0] = 1;
                g80[1] = 1;
                g80[2] = 4;
                g80[3] = 8;
                g80[4] = 18;
                g80[5] = 19;
                break;
            default:
                Arrays.fill(g50, 0);
                Arrays.fill(g80, 0);
                break;
        }
    }

    private byte[] getReqUpdateBuffer() {
        ByteBuffer buffer = ByteBuffer.allocate(160);
        buffer.order(ByteOrder.LITTLE_ENDIAN);

        buffer.putInt(noOp);
        buffer.putInt(afc);
        buffer.putInt(feedback);
        buffer.putInt(rearMics);
        for (int i = 0; i < NUM_BANDS; i++) {
            buffer.putInt(g50[i]);
        }
        for (int i = 0; i < NUM_BANDS; i++) {
            buffer.putInt(g80[i]);
        }
        for (int i = 0; i < NUM_BANDS; i++) {
            buffer.putInt(kneeLow[i]);
        }
        for (int i = 0; i < NUM_BANDS; i++) {
            buffer.putInt(MPOLimit[i]);
        }
        for (int i = 0; i < NUM_BANDS; i++) {
            buffer.putInt(attackTime[i]);
        }
        for (int i = 0; i < NUM_BANDS; i++) {
            buffer.putInt(releaseTime[i]);
        }

        return buffer.array();
    }

    public int getUnderruns() {
        byte[] req = new byte[1];
        req[0] = REQUEST_GET_UNDERRUNS;
        ospTcp.sendMessage(req);
        underruns = Integer.reverseBytes(ospTcp.getUnderruns());
        return underruns;
    }


    public void setToDefaultParams() {
        maxFullness = 21;
        maxVolume = 40;
        maxCrispness = 7;
        fullnessStep = 2;
        volumeStep = 2;
        crispnessStep = 1;
        fullnessLevel = 5;
        volumeLevel = 10;
        crispnessLevel = 3;
        fullness = fullnessStep * fullnessLevel;
        volume = volumeStep * volumeLevel;
        crispness = crispnessStep * crispnessLevel;
        int[] boothroydCFreq = new int[]{177, 354, 707, 1414, 2828, 5657};
        int[] defaultMultipliers = new int[]{1, 2, 3, 3};
        for (int i = 0; i < 4; i++) {
            crispnessMultipliers[i] = defaultMultipliers[i];
        }

        Arrays.fill(compRatio, 1.0f);
        g65[0] = volume - fullness;
        g65[1] = volume;
        for(int i=2; i<NUM_BANDS; i++) {
            g65[i] = volume + crispness * crispnessMultipliers[i-2];
        }
        for(int i=0; i<NUM_BANDS; i++) {
            float slope = (1-compRatio[i]) / compRatio[i];
            g50[i] = (int) (g65[i] - slope*(65-50));
            g80[i] = (int) (g65[i] + slope*(80-65));
            MPOLimit[i] = 100;
            kneeLow[i] = 45;
            attackTime[i] = 5;
            releaseTime[i] = 20;
            cFreq[i] = boothroydCFreq[i];
        }
    }

    public int[] getCFreq() {
        return cFreq;
    }

    public void setCFreq(int[] cFreq) {
        this.cFreq = cFreq;
    }

    public String getResearcherInitials() {
        return researcherInitials;
    }

    public void setResearcherInitials(String researcherInitials) {
        this.researcherInitials = researcherInitials;
    }

    public class OspInterfaceException extends Exception {
        public OspInterfaceException(String message) {
            super(message);
        }
    }

    private static final OspInterface params = new OspInterface();

    public static OspInterface getInstance() {
        return params;
    }

}

