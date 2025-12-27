package com.ctis487.project.digitalgamemarket

import android.content.Context
import android.content.SharedPreferences
import android.content.res.Configuration
import java.util.*
import androidx.core.content.edit

object LocaleHelper {
    private const val SELECTED_LANGUAGE = "Locale.Helper.Selected.Language"

    fun setLocale(context: Context, language: String): Context {
        persist(context, language)
        return updateResources(context, language)
    }

    fun getLanguage(context: Context): String {
        return getPersistedData(context, Locale.getDefault().language)
    }

    private fun persist(context: Context, language: String) {
        val preferences = getPreferences(context)
        preferences.edit { putString(SELECTED_LANGUAGE, language) }
    }

    private fun getPersistedData(context: Context, defaultLanguage: String): String {
        val preferences = getPreferences(context)
        return preferences.getString(SELECTED_LANGUAGE, defaultLanguage) ?: defaultLanguage
    }

    private fun getPreferences(context: Context): SharedPreferences {
        return context.getSharedPreferences("app_preferences", Context.MODE_PRIVATE)
    }

    private fun updateResources(context: Context, language: String): Context {
        val locale = Locale(language)
        Locale.setDefault(locale)

        val configuration = Configuration(context.resources.configuration)
        configuration.setLocale(locale)

        return context.createConfigurationContext(configuration)
    }
}

