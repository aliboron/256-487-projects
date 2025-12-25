package com.ctis487.project.digitalgamemarket.custom

import android.content.Context
import android.util.AttributeSet
import android.view.LayoutInflater
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.TextView
import androidx.annotation.DrawableRes
import com.ctis487.project.digitalgamemarket.R

class CustomUserView @JvmOverloads constructor(
    context: Context,
    attrs: AttributeSet? = null
) : LinearLayout(context, attrs) {

    private val imgProfile: ImageView
    private val tvName: TextView

    init {
        LayoutInflater.from(context).inflate(R.layout.view_profile, this, true)

        imgProfile = findViewById(R.id.imgProfile)
        tvName = findViewById(R.id.tvName)

        orientation = VERTICAL
    }


    fun setName(name: String) {
        tvName.text = name
    }
    fun getName(): String {
        return tvName.text.toString()
    }


}
