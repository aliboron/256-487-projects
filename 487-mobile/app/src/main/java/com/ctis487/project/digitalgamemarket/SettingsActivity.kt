package com.ctis487.project.digitalgamemarket

import android.content.Context
import android.os.Bundle
import android.widget.Toast
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import com.ctis487.project.digitalgamemarket.databinding.ActivitySettingsBinding

class SettingsActivity : AppCompatActivity() {
    private lateinit var binding: ActivitySettingsBinding

    override fun attachBaseContext(newBase: Context) {
        super.attachBaseContext(LocaleHelper.setLocale(newBase, LocaleHelper.getLanguage(newBase)))
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()

        binding = ActivitySettingsBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // Set current language selection
        val currentLang = LocaleHelper.getLanguage(this)
        when (currentLang) {
            "en" -> binding.radioEnglish.isChecked = true
            "tr" -> binding.radioTurkish.isChecked = true
        }

        // Update current language text
        updateCurrentLanguageText(currentLang)

        // Handle radio group selection changes
        binding.radioGroupLanguage.setOnCheckedChangeListener { _, checkedId ->
            val selectedLang = when (checkedId) {
                R.id.radioEnglish -> "en"
                R.id.radioTurkish -> "tr"
                else -> currentLang
            }
            updateCurrentLanguageText(selectedLang)
        }

        // Apply language button
        binding.btnApplyLanguage.setOnClickListener {
            val selectedLang = when (binding.radioGroupLanguage.checkedRadioButtonId) {
                R.id.radioEnglish -> "en"
                R.id.radioTurkish -> "tr"
                else -> currentLang
            }

            if (selectedLang != currentLang) {
                LocaleHelper.setLocale(this, selectedLang)
                Toast.makeText(
                    this,
                    getString(R.string.toast_language_changed),
                    Toast.LENGTH_SHORT
                ).show()
                recreate()
            } else {
                Toast.makeText(
                    this,
                    getString(R.string.toast_language_already_selected),
                    Toast.LENGTH_SHORT
                ).show()
            }
        }

        // Back button
        binding.btnBack.setOnClickListener {
            finish()
        }
    }

    private fun updateCurrentLanguageText(languageCode: String) {
        val languageName = when (languageCode) {
            "en" -> getString(R.string.language_english)
            "tr" -> getString(R.string.language_turkish)
            else -> getString(R.string.language_english)
        }
        binding.txtCurrentLanguage.text = getString(R.string.txt_current_language_format, languageName)
    }
}

