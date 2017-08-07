package edu.ucsd.hearingaid.osp_r01;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.Toast;

import java.util.ArrayList;
import java.util.HashSet;
import java.util.List;
import java.util.Set;
import java.util.regex.Matcher;
import java.util.regex.Pattern;


/**
 * A login screen
 */
public class LoginActivity extends AppCompatActivity {

    // UI references.
    private EditText mIPAddressView;
    private EditText mUserInitialView, mResearcherInitialView;

    private String ipAddress;
    private boolean isValidIP = false;
    String senderIP, message;
    private static final Pattern IP_ADDRESS = Pattern.compile(
            "((25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[1-9][0-9]|[1-9])\\.(25[0-5]|2[0-4]"
                    + "[0-9]|[0-1][0-9]{2}|[1-9][0-9]|[1-9]|0)\\.(25[0-5]|2[0-4][0-9]|[0-1]"
                    + "[0-9]{2}|[1-9][0-9]|[1-9]|0)\\.(25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}"
                    + "|[1-9][0-9]|[0-9]))");

    private ReceiveMessages myReceiver = null;
    private Boolean myReceiverIsRegistered = false;
    private Spinner listenerSpinner;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);
        myReceiver = new ReceiveMessages();
        Intent intent = new Intent(this, UDPListenerService.class);
        startService(intent);

        listenerSpinner = (Spinner) findViewById(R.id.listener_spinner);
        listenerSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                Toast.makeText(parent.getContext(),
                        "OnItemSelectedListener : " + parent.getItemAtPosition(position).toString(),
                        Toast.LENGTH_SHORT).show();
                mIPAddressView.setText(senderIP);
                isValidIP = true;
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {

            }
        });

        mIPAddressView = (EditText) findViewById(R.id.ip_text);
        mIPAddressView.setOnFocusChangeListener(new View.OnFocusChangeListener() {
            @Override
            public void onFocusChange(View v, boolean hasFocus) {
                ipAddress = mIPAddressView.getText().toString();
                if (!hasFocus) {
                    Matcher matcher = IP_ADDRESS.matcher(ipAddress);
                    if (!matcher.matches()) {
                        Toast.makeText(getBaseContext(), "Enter a valid IP address", Toast.LENGTH_SHORT).show();
                        mIPAddressView.setError(getString(R.string.invalid_ip));
                        isValidIP = false;
                    } else {
                        isValidIP = true;
                    }
                }
            }
        });

        mUserInitialView = (EditText) findViewById(R.id.user_initials);
        mResearcherInitialView = (EditText) findViewById(R.id.researcher_initials);
        Button mLoginButton = (Button) findViewById(R.id.login_btn);
        mLoginButton.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View view) {
                mIPAddressView.clearFocus();
                String userInitials = mUserInitialView.getText().toString();
                String researcherInitials = mResearcherInitialView.getText().toString();
                ipAddress = mIPAddressView.getText().toString();
                OspInterface.getInstance().setUserInitals(userInitials);
                OspInterface.getInstance().setResearcherInitials(researcherInitials);
                if (userInitials.matches("")) {
                    Toast.makeText(getBaseContext(), "You did not enter User ID", Toast.LENGTH_SHORT).show();
                    mUserInitialView.setError(getString(R.string.user_initials));
                    return;
                }
                if (researcherInitials.matches("")) {
                    Toast.makeText(getBaseContext(), "You did not enter Researcher Initials", Toast.LENGTH_SHORT).show();
                    mResearcherInitialView.setError(getString(R.string.researcher_initials));
                    return;
                }
                if (isValidIP) {
                    new Thread(new Runnable() {
                        public void run() {
                            System.out.println("IP address is " + ipAddress);
                            try {
                                String stringToSend = OspInterface.getInstance().getResearcherInitials() +"_"+ OspInterface.getInstance().getUserInitals();
                                OspInterface.getInstance().connect(ipAddress, stringToSend);
                            } catch (OspInterface.OspInterfaceException e) {
                                e.printStackTrace();
                                Log.i("Login", e.getStackTrace().toString());
                            }
                            if (OspInterface.getInstance().isConnected()) {
                                LoginActivity.this.runOnUiThread(new Runnable() {
                                    public void run() {
                                        Toast.makeText(getBaseContext(), "Connected", Toast.LENGTH_SHORT).show();
                                    }
                                });
                            } else {
                                LoginActivity.this.runOnUiThread(new Runnable() {
                                    public void run() {
                                        Toast.makeText(getBaseContext(), "Not Connected", Toast.LENGTH_SHORT).show();
                                    }
                                });
                            }
                        }
                    }).start();
                } else {
                    Toast.makeText(getBaseContext(), "Enter a valid IP address", Toast.LENGTH_SHORT).show();
                    mIPAddressView.setError(getString(R.string.invalid_ip));
                }

                OspInterface.getInstance().setToDefaultParams();
                Intent intent = new Intent(LoginActivity.this, MainActivity.class);
                startActivity(intent);
            }
        });

    }

    @Override
    protected void onResume() {
        super.onResume();
        if (!myReceiverIsRegistered) {
            registerReceiver(myReceiver, new IntentFilter(UDPListenerService.UDP_BROADCAST));
            myReceiverIsRegistered = true;
        }
    }

    @Override
    protected void onPause() {
        super.onPause();
        if (myReceiverIsRegistered) {
            unregisterReceiver(myReceiver);
            myReceiverIsRegistered = false;
        }
    }

    public class ReceiveMessages extends BroadcastReceiver {
        @Override
        public void onReceive(Context context, Intent intent) {
            senderIP = intent.getExtras().getString("sender");
            message = intent.getExtras().getString("message");
            Set<String> set = new HashSet<>();
            set.add(message + " ("+senderIP+")");
            List<String> list = new ArrayList<>(set);
            ArrayAdapter<String> dataAdapter = new ArrayAdapter<>(getBaseContext(),
                    android.R.layout.simple_spinner_item, list);
            dataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
            listenerSpinner.setAdapter(dataAdapter);
            listenerSpinner.setSelection(0);
//            Intent serviceIntent = new Intent(LoginActivity.this, UDPListenerService.class);
//            getBaseContext().stopService(serviceIntent);
        }
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        Intent intent = new Intent(this, UDPListenerService.class);
        stopService(intent);
    }
}

