package com.example.collegeapp

import android.content.Intent
import android.os.Bundle
import android.view.inputmethod.EditorInfo
import android.view.inputmethod.InputMethodManager
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import com.example.collegeapp.R
import com.example.collegeapp.databinding.ActivityWeatherBinding
import com.example.collegeapp.ui.weather.DetailWeatherActivity
import com.example.collegeapp.ui.weather.WeatherAdapter
import com.example.collegeapp.ui.weather.WeatherViewModel

class WeatherActivity : AppCompatActivity() {

    private lateinit var binding: ActivityWeatherBinding
    private lateinit var weatherAdapter: WeatherAdapter
    private val weatherViewModel: WeatherViewModel by viewModels()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityWeatherBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setSupportActionBar(binding.toolbar)
        supportActionBar?.title = getString(R.string.weather_forecast_title)

        setupRecyclerView()
        observeViewModel()
        setupSearch()

        // Запрашиваем погоду для города по умолчанию (Москва)
        weatherViewModel.fetchWeather("Moscow")
    }

    private fun setupSearch() {
        binding.searchButton.setOnClickListener {
            performSearch()
        }

        binding.cityEditText.setOnEditorActionListener { _, actionId, _ ->
            if (actionId == android.view.inputmethod.EditorInfo.IME_ACTION_SEARCH) {
                performSearch()
                true
            } else {
                false
            }
        }
    }

    private fun performSearch() {
        val cityName = binding.cityEditText.text.toString()
        if (cityName.isNotBlank()) {
            weatherViewModel.fetchWeather(cityName)
            hideKeyboard()
        }
    }

    private fun hideKeyboard() {
        val imm = getSystemService(INPUT_METHOD_SERVICE) as android.view.inputmethod.InputMethodManager
        imm.hideSoftInputFromWindow(binding.cityEditText.windowToken, 0)
    }

    private fun setupRecyclerView() {
        weatherAdapter = WeatherAdapter(emptyList()) { forecast ->
            val intent = Intent(this, DetailWeatherActivity::class.java)
            intent.putExtra("forecast", forecast)
            startActivity(intent)
        }
        binding.recyclerView.apply {
            adapter = weatherAdapter
            layoutManager = LinearLayoutManager(this@WeatherActivity)
        }
    }

    private fun observeViewModel() {
        weatherViewModel.weatherData.observe(this) { weatherResponse ->
            weatherAdapter.updateData(weatherResponse.list, weatherResponse.city)
        }

        weatherViewModel.error.observe(this) { errorMessage ->
            val formattedError = getString(R.string.weather_fetch_error, errorMessage)
            android.widget.Toast.makeText(this, formattedError, android.widget.Toast.LENGTH_LONG).show()
        }
    }
}
