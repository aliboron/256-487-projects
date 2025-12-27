package com.ctis487.project.digitalgamemarket

import android.content.Intent
import android.os.Bundle
import android.widget.Toast
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.databinding.DataBindingUtil
import androidx.lifecycle.lifecycleScope
import com.ctis487.project.digitalgamemarket.client.ApiClient
import com.ctis487.project.digitalgamemarket.client.GameMarketApiService
import com.ctis487.project.digitalgamemarket.custom.CustomUserView
import com.ctis487.project.digitalgamemarket.databinding.ActivityLoginBinding
import com.ctis487.project.digitalgamemarket.model.LoginApiRequest
import com.ctis487.project.digitalgamemarket.model.LoginRequest
import kotlinx.coroutines.launch

class LoginActivity : AppCompatActivity() {
    lateinit var loginBinding: ActivityLoginBinding
    private val loginRequest = LoginRequest()
    private val apiService by lazy { ApiClient.getClient().create(GameMarketApiService::class.java) }
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        loginBinding = ActivityLoginBinding.inflate(layoutInflater)
        val profileView = findViewById<CustomUserView>(R.id.profileView)


        loginBinding = DataBindingUtil.setContentView(this, R.layout.activity_login)
        loginBinding.loginRequest=loginRequest
        loginBinding.btnLogin.setOnClickListener {
            var username=loginBinding.editUsername.text.toString()
            var password=loginBinding.editPassword.text.toString()

            if (username.isEmpty() || password.isEmpty()) {
                Toast.makeText(this@LoginActivity,"Username and password required", Toast.LENGTH_LONG).show()
                return@setOnClickListener
            }

            lifecycleScope.launch {

                try {
                    val response = apiService.login(
                        LoginApiRequest(username, password)
                    )

                    if (response.isSuccessful && response.body()?.success == true) {


                        val intent = Intent(this@LoginActivity, MainActivity::class.java)
                        startActivity(intent)
                        finish()

                    } else {
                        val errorMessage =
                            response.body()?.message ?: "Login failed"
                        Toast.makeText(this@LoginActivity,errorMessage, Toast.LENGTH_LONG).show()
                    }

                } catch (e: Exception) {
                    Toast.makeText(
                        this@LoginActivity,
                        "Network error: ${e.localizedMessage}",
                        Toast.LENGTH_LONG
                    ).show()
                }
            }
        }


    }



}
