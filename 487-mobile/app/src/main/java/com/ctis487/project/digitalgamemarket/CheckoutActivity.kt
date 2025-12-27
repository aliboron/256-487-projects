package com.ctis487.project.digitalgamemarket

import android.content.Context
import android.os.Bundle
import android.text.Editable
import android.text.TextWatcher
import android.util.Log
import android.widget.Toast
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.res.ResourcesCompat
import androidx.lifecycle.lifecycleScope
import com.bumptech.glide.Glide
import com.ctis487.project.digitalgamemarket.client.ApiClient
import com.ctis487.project.digitalgamemarket.client.GameMarketApiService
import com.ctis487.project.digitalgamemarket.databinding.ActivityCheckoutBinding
import com.ctis487.project.digitalgamemarket.db.DigitalGameAssetRoomDatabase
import com.ctis487.project.digitalgamemarket.db.GameRepository
import com.ctis487.project.digitalgamemarket.db.Utils
import com.ctis487.project.digitalgamemarket.model.Checkout
import com.ctis487.project.digitalgamemarket.model.Game
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import www.sanju.motiontoast.MotionToast
import www.sanju.motiontoast.MotionToastStyle
import java.text.SimpleDateFormat
import java.util.*

class CheckoutActivity : AppCompatActivity() {
    lateinit var binding: ActivityCheckoutBinding
    private var currentGame: Game? = null
    private val apiService by lazy { ApiClient.getClient().create(GameMarketApiService::class.java) }
    private val db by lazy { DigitalGameAssetRoomDatabase.getDatabase(this) }

    override fun attachBaseContext(newBase: Context) {
        super.attachBaseContext(LocaleHelper.setLocale(newBase, LocaleHelper.getLanguage(newBase)))
    }


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()

        binding = ActivityCheckoutBinding.inflate(layoutInflater)
        setContentView(binding.root)

        val gameId = intent.getIntExtra("gameId", 21)

        val gameRepository = GameRepository(db.gameDAO(), apiService)

        // Load game data
        db.gameDAO().getGameById(gameId).observe(this) { gameOrNull ->
            if (gameOrNull != null) {
                currentGame = gameOrNull
                renderGame(gameOrNull)
            } else {
                Log.d("CheckoutActivity", "Game $gameId not in DB. Fetching from API.")
                lifecycleScope.launch(Dispatchers.IO) {
                    val result: Result<Game> = gameRepository.fetchGameByIdFromApi(gameId)

                    val apiGame: Game? = result.getOrNull()
                    if (apiGame != null) {
                        withContext(Dispatchers.Main) {
                            currentGame = apiGame
                            renderGame(apiGame)
                        }
                        db.gameDAO().insert(apiGame)
                    } else
                        Log.e("CheckoutActivity", "API fetch failed", result.exceptionOrNull())
                }
            }
        }

        binding.checkoutBtnCheckout.setOnClickListener {
            processCheckout()
        }

        binding.checkoutBtnCancel.setOnClickListener {
            finish()
        }

        binding.editTextCardNumber.addTextChangedListener(object : TextWatcher {
            private var isFormatting = false
            private var deletingSpace = false

            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {
                // Check if user is deleting a space
                if (count > 0 && after == 0) {
                    val charDeleted = s?.getOrNull(start)
                    deletingSpace = charDeleted == ' '
                }
            }

            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {}

            override fun afterTextChanged(s: Editable?) {
                if (isFormatting || s == null) return

                isFormatting = true

                // Remove all spaces
                val digitsOnly = s.toString().replace(" ", "")

                // 16 digits
                val trimmed = if (digitsOnly.length > 16) {
                    digitsOnly.take(16)
                } else {
                    digitsOnly
                }

                val formatted = StringBuilder()
                for (i in trimmed.indices) {
                    if (i > 0 && i % 4 == 0) {
                        formatted.append(' ')
                    }
                    formatted.append(trimmed[i])
                }

                val formattedString = formatted.toString()
                if (formattedString != s.toString()) {
                    s.replace(0, s.length, formattedString)
                }

                isFormatting = false
            }
        })

        binding.editTextExpiryDate.addTextChangedListener(object : TextWatcher {
            private var isFormatting = false

            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {}

            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {}

            override fun afterTextChanged(s: Editable?) {
                if (isFormatting || s == null) return

                isFormatting = true

                val digitsOnly = s.toString().replace("/", "")

                // Limit to 4 digits (MMYY)
                val trimmed = if (digitsOnly.length > 4) {
                    digitsOnly.take(4)
                } else {
                    digitsOnly
                }

                val formatted = if (trimmed.length >= 2) {
                    "${trimmed.take(2)}/${trimmed.substring(2)}"
                } else {
                    trimmed
                }

                if (formatted != s.toString()) {
                    s.replace(0, s.length, formatted)
                }

                isFormatting = false
            }
        })
    }

    fun renderGame(game: Game) {
        binding.checkoutTxtGameTitle.text = game.name
        binding.checkoutTxtGamePrice.text = getString(R.string.format_price, game.price.toString())
        binding.checkoutTxtDetails.text = getString(R.string.format_total, game.price, game.name)
        binding.checkoutTxtGameDetails.text = game.description

        Glide.with(this)
            .load(game.logoPath)
            .placeholder(R.drawable.ic_launcher_foreground)
            .error(R.drawable.ic_launcher_foreground)
            .centerCrop()
            .into(binding.imageView)
    }

    private fun processCheckout() {
        val cardName = binding.editTextCardName.text.toString().trim()
        val cardNumber = binding.editTextCardNumber.text.toString().trim()
        val expiryDate = binding.editTextExpiryDate.text.toString().trim()
        val cvc = binding.editTextCVC.text.toString().trim()

        if (!validateInputs(cardName, cardNumber, expiryDate, cvc)) {
            return
        }

        val game = currentGame
        if (game == null) {
            Toast.makeText(this, "Game information not loaded", Toast.LENGTH_SHORT).show()
            return
        }

        val userId = Utils.user?.user?.id
        if (userId == null) {
            Toast.makeText(this, "User information not loaded", Toast.LENGTH_SHORT).show()
            return
        }

        // Create checkout object
        val dateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        val currentDate = dateFormat.format(Date())

        val checkout = Checkout(
            date = currentDate,
            userId = userId,
            paymentTotal = game.price,
            gameId = game.id
        )

        lifecycleScope.launch(Dispatchers.IO) {
            try {
                db.checkoutDAO().insert(checkout)
                val response = apiService.createCheckout(checkout)

                withContext(Dispatchers.Main) {
                    if (response.isSuccessful && response.body()?.success == true) {
                        MotionToast.setSuccessBackgroundColor(com.google.android.material.R.color.design_default_color_primary)
                        MotionToast.createToast(this@CheckoutActivity,
                            "Hurray success 😍",
                            "Upload Completed successfully!",
                            MotionToastStyle.SUCCESS,
                            MotionToast.GRAVITY_BOTTOM,
                            MotionToast.LONG_DURATION,
                            ResourcesCompat.getFont(this@CheckoutActivity, www.sanju.motiontoast.R.font.helvetica_regular))

                        finish()
                    } else {
                        Toast.makeText(
                            this@CheckoutActivity,
                            getString(R.string.toast_checkout_failed, response.body()?.message ?: "Unknown error"),
                            Toast.LENGTH_LONG
                        ).show()
                    }
                }
            } catch (e: Exception) {
                Log.e("CheckoutActivity", "Checkout error", e)
                withContext(Dispatchers.Main) {
                    Toast.makeText(
                        this@CheckoutActivity,
                        getString(R.string.toast_checkout_error, e.message),
                        Toast.LENGTH_LONG
                    ).show()


                }
            }
        }
    }

    private fun validateInputs(
        cardName: String,
        cardNumber: String,
        expiryDate: String,
        cvc: String
    ): Boolean {
        if (cardName.isEmpty()) {
            binding.editTextCardName.error = getString(R.string.error_card_name_required)
            binding.editTextCardName.requestFocus()
            return false
        }

        if (cardName.length < 3) {
            binding.editTextCardName.error = getString(R.string.error_card_name_too_short)
            binding.editTextCardName.requestFocus()
            return false
        }

        if (cardNumber.isEmpty()) {
            binding.editTextCardNumber.error = getString(R.string.error_card_number_required)
            binding.editTextCardNumber.requestFocus()
            return false
        }

        val cleanCardNumber = cardNumber.replace("\\s".toRegex(), "")
        if (!cleanCardNumber.matches("\\d{16}".toRegex())) {
            binding.editTextCardNumber.error = getString(R.string.error_card_number_invalid_length)
            binding.editTextCardNumber.requestFocus()
            return false
        }

        if (!isValidCardNumber(cleanCardNumber)) {
            binding.editTextCardNumber.error = getString(R.string.error_card_number_invalid)
            binding.editTextCardNumber.requestFocus()
            return false
        }

        if (expiryDate.isEmpty()) {
            binding.editTextExpiryDate.error = getString(R.string.error_expiry_required)
            binding.editTextExpiryDate.requestFocus()
            return false
        }

        if (!isValidExpiryDate(expiryDate)) {
            binding.editTextExpiryDate.error = getString(R.string.error_expiry_invalid)
            binding.editTextExpiryDate.requestFocus()
            return false
        }

        if (cvc.isEmpty()) {
            binding.editTextCVC.error = getString(R.string.error_cvc_required)
            binding.editTextCVC.requestFocus()
            return false
        }

        if (!cvc.matches("\\d{3}".toRegex())) {
            binding.editTextCVC.error = getString(R.string.error_cvc_invalid)
            binding.editTextCVC.requestFocus()
            return false
        }

        return true
    }

    private fun isValidCardNumber(cardNumber: String): Boolean {
        var sum = 0
        var alternate = false

        for (i in cardNumber.length - 1 downTo 0) {
            var digit = cardNumber[i].toString().toInt()

            if (alternate) {
                digit *= 2
                if (digit > 9) {
                    digit -= 9
                }
            }

            sum += digit
            alternate = !alternate
        }

        return sum % 10 == 0
    }

    private fun isValidExpiryDate(expiryDate: String): Boolean {
        val parts = expiryDate.split("/")
        if (parts.size != 2) {
            return false
        }

        val month = parts[0].toIntOrNull() ?: return false
        val year = parts[1].toIntOrNull() ?: return false

        if (month !in 1..12) {
            return false
        }

        val fullYear = if (year < 100) {
            2000 + year
        } else {
            year
        }

        val currentCalendar = Calendar.getInstance()
        val currentYear = currentCalendar.get(Calendar.YEAR)
        val currentMonth = currentCalendar.get(Calendar.MONTH) + 1

        if (fullYear < currentYear) {
            return false
        }

        if (fullYear == currentYear && month < currentMonth) {
            return false
        }

        return true
    }
}