package com.example.collegeapp.features.weather.presentation.ui

import android.content.Intent
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.view.inputmethod.InputMethodManager
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.fragment.app.Fragment
import androidx.recyclerview.widget.LinearLayoutManager
import com.example.collegeapp.R
import com.example.collegeapp.databinding.FragmentWeatherBinding
import com.example.collegeapp.features.weather.presentation.state.WeatherState
import com.example.collegeapp.features.weather.presentation.viewmodel.WeatherViewModel
import org.koin.androidx.viewmodel.ext.android.viewModel

class WeatherFragment : Fragment() {

    private var _binding: FragmentWeatherBinding? = null
    private val binding get() = _binding!!

    private lateinit var weatherAdapter: WeatherAdapter
    private val weatherViewModel: WeatherViewModel by viewModel()

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = FragmentWeatherBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        setupToolbar()
        setupRecyclerView()
        observeViewModel()
        setupSearch()

        // Запрашиваем погоду для города по умолчанию (Москва)
        weatherViewModel.fetchWeather("Moscow")
    }

    private fun setupToolbar() {
        (activity as? AppCompatActivity)?.setSupportActionBar(binding.toolbar)
        (activity as? AppCompatActivity)?.supportActionBar?.title = getString(R.string.weather_forecast_title)
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
        val imm = requireContext().getSystemService(AppCompatActivity.INPUT_METHOD_SERVICE) as InputMethodManager
        imm.hideSoftInputFromWindow(binding.cityEditText.windowToken, 0)
    }

    private fun setupRecyclerView() {
        weatherAdapter = WeatherAdapter(emptyList(), emptyList()) { forecast ->
            val intent = Intent(requireContext(), DetailWeatherActivity::class.java)
            intent.putExtra("forecast", forecast)
            startActivity(intent)
        }
        binding.recyclerView.apply {
            adapter = weatherAdapter
            layoutManager = LinearLayoutManager(requireContext())
        }
    }

    private fun observeViewModel() {
        weatherViewModel.state.observe(viewLifecycleOwner) { state ->
            when (state) {
                is WeatherState.Idle -> {
                    // Начальное состояние
                }
                is WeatherState.Loading -> {
                    // Можно показать прогресс бар
                }
                is WeatherState.Success -> {
                    weatherAdapter.updateData(state.weatherForecast.forecasts, state.weatherForecast.city)
                }
                is WeatherState.Error -> {
                    val formattedError = getString(R.string.weather_fetch_error, state.message)
                    Toast.makeText(requireContext(), formattedError, Toast.LENGTH_LONG).show()
                }
            }
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
