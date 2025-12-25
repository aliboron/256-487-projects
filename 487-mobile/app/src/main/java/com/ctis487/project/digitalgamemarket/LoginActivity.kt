package com.ctis487.project.digitalgamemarket

import android.content.Intent
import android.os.Bundle
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.databinding.DataBindingUtil
import com.ctis487.project.digitalgamemarket.custom.CustomUserView
import com.ctis487.project.digitalgamemarket.databinding.ActivityLoginBinding
import com.ctis487.project.digitalgamemarket.model.LoginRequest

class LoginActivity : AppCompatActivity() {
    lateinit var loginBinding: ActivityLoginBinding
    private val loginRequest = LoginRequest()
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        loginBinding = ActivityLoginBinding.inflate(layoutInflater)
        val profileView = findViewById<CustomUserView>(R.id.profileView)
        val username=loginBinding.editUsername.text

        loginBinding = DataBindingUtil.setContentView(this, R.layout.activity_login)
        loginBinding.loginRequest=loginRequest

    }



}
