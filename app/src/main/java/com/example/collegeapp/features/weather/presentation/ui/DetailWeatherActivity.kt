package com.example.collegeapp.features.weather.presentation.ui

import android.os.Build
import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import com.example.collegeapp.R
import com.example.collegeapp.databinding.ActivityDetailWeatherBinding
import com.example.collegeapp.features.weather.domain.model.ForecastItem

class DetailWeatherActivity : AppCompatActivity() {

    private lateinit var binding: ActivityDetailWeatherBinding

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityDetailWeatherBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setSupportActionBar(binding.toolbar)
        supportActionBar?.setDisplayHomeAsUpEnabled(true)

        val forecast = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            intent.getSerializableExtra("forecast", ForecastItem::class.java)
        } else {
            @Suppress("DEPRECATION")
            intent.getSerializableExtra("forecast") as? ForecastItem
        }

        if (forecast != null) {
            supportActionBar?.title = forecast.dateTimeText
            displayForecastDetails(forecast)
        }
    }

    override fun onSupportNavigateUp(): Boolean {
        finish()
        return true
    }

    private fun displayForecastDetails(forecast: ForecastItem) {
        binding.humidityTextView.text = getString(R.string.humidity_format, forecast.humidity)
        binding.windSpeedTextView.text = getString(R.string.wind_speed_format, forecast.windSpeed)
        binding.pressureTextView.text = getString(R.string.pressure_format, forecast.pressure)
    }
}
