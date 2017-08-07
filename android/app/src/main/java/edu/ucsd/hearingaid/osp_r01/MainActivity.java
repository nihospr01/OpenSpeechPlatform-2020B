package edu.ucsd.hearingaid.osp_r01;

import android.content.Intent;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;


/**
 * A login screen
 */
public class MainActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        String userInitials = OspInterface.getInstance().getUserInitals();
        String researcherInitials = OspInterface.getInstance().getResearcherInitials();

        TextView mUserInitialsView = (TextView) findViewById(R.id.user_initials_txt);
        String userInitialsTextViewString = getString(R.string.user_initials_txt) + " " + userInitials;
        mUserInitialsView.setText(userInitialsTextViewString);
        TextView mResearcherInitialsView = (TextView) findViewById(R.id.resercher_initials_txt);
        String researcherInitialsTextViewString = getString(R.string.researcher_initials_txt) + " " + researcherInitials;
        mResearcherInitialsView.setText(researcherInitialsTextViewString);

        Button mSettingsPageButton = (Button) findViewById(R.id.settings_page_btn);
        mSettingsPageButton.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View view) {
                // TODO: Add call to settings page
                Intent intent = new Intent(MainActivity.this, SettingsActivity.class);
                startActivity(intent);
            }
        });

        Button mResearcherPagesButton = (Button) findViewById(R.id.researcher_pages_btn);
        mResearcherPagesButton.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View view) {
                // TODO: Add call to researcher pages
                Intent intent = new Intent(MainActivity.this, ResearcherPagesActivity.class);
                startActivity(intent);
            }
        });



    }

    @Override
    public void onBackPressed() {
        OspInterface.getInstance().stopClient();
        Toast.makeText(getBaseContext(), "Disconnected", Toast.LENGTH_SHORT).show();
        finish();
    }
}

