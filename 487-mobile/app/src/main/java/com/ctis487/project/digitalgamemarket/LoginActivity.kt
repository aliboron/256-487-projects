package com.ctis487.project.digitalgamemarket

import android.content.Intent
import android.os.Bundle
import android.util.Log
import android.view.GestureDetector
import android.view.MotionEvent
import android.widget.Toast
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.databinding.DataBindingUtil
import androidx.lifecycle.lifecycleScope
import com.bumptech.glide.util.Util
import com.ctis487.project.digitalgamemarket.client.ApiClient
import com.ctis487.project.digitalgamemarket.client.GameMarketApiService
import com.ctis487.project.digitalgamemarket.custom.CustomUserView
import com.ctis487.project.digitalgamemarket.databinding.ActivityLoginBinding
import com.ctis487.project.digitalgamemarket.db.DigitalGameAssetRoomDatabase
import com.ctis487.project.digitalgamemarket.db.GameRepository
import com.ctis487.project.digitalgamemarket.db.UserRepository
import com.ctis487.project.digitalgamemarket.db.Utils
import com.ctis487.project.digitalgamemarket.model.LoginApiRequest
import com.ctis487.project.digitalgamemarket.model.LoginRequest
import com.ctis487.project.digitalgamemarket.model.LoginResponseDto
import com.ctis487.project.digitalgamemarket.model.User
import kotlinx.coroutines.launch

class LoginActivity : AppCompatActivity() {
    lateinit var loginBinding: ActivityLoginBinding
    lateinit var db : DigitalGameAssetRoomDatabase
    private val loginRequest = LoginRequest()

    lateinit var userRepo : UserRepository

    lateinit var gameRepo : GameRepository
    private val apiService by lazy { ApiClient.getClient().create(GameMarketApiService::class.java)

    }
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

                        Toast.makeText(this@LoginActivity,"Login successful", Toast.LENGTH_LONG).show()
                        Utils.user= response.body()?.data
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

        db = DigitalGameAssetRoomDatabase.getDatabase(application)
        userRepo = UserRepository(db.userDAO(), ApiClient.getClient().create(GameMarketApiService::class.java))
        gameRepo = GameRepository(db.gameDAO(), ApiClient.getClient().create(GameMarketApiService::class.java))

        lifecycleScope.launch {
            val result = userRepo.fetchUsersFromApi()
            Log.wtf("API USER CHECK", result.isSuccess.toString())
            gameRepo.fetchGamesFromApi()
            val mediaResult = apiService.getMedia()
            if (mediaResult.isSuccessful){
                Log.d("API MEDIA CHECK", mediaResult.body()!!.data!!.joinToString(" "))
                Utils.bannerImages.addAll(mediaResult.body()!!.data!!)
            }
        }

        loginBinding.btnLogin.setOnLongClickListener {
            AlertDialog.Builder(this@LoginActivity)
                .setTitle("EASTER EGG")
                .setMessage("WOW WOW WOW YOU HAVE FOUND AN EASTER EGG")
                .setPositiveButton("YES") { dialog, _ ->
                    dialog.dismiss()
                }
                .setNegativeButton("NO") { dialog, _ ->
                    // close app
                    finishAffinity()
                }
                .show()
            true
        }
    }
}
