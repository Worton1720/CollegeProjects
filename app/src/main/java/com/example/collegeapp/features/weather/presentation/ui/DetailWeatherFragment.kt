package com.example.collegeapp.features.weather.presentation.ui

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.appcompat.app.AppCompatActivity
import androidx.fragment.app.Fragment
import androidx.navigation.fragment.findNavController
import androidx.navigation.fragment.navArgs
import com.example.collegeapp.R
import com.example.collegeapp.databinding.FragmentDetailWeatherBinding
import com.example.collegeapp.features.weather.domain.model.ForecastItem

class DetailWeatherFragment : Fragment() {

    private var _binding: FragmentDetailWeatherBinding? = null
    private val binding get() = _binding!!

    private val args: DetailWeatherFragmentArgs by navArgs()

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = FragmentDetailWeatherBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        setupToolbar()
        displayForecastDetails(args.forecast)
    }

    private fun setupToolbar() {
        (activity as? AppCompatActivity)?.setSupportActionBar(binding.toolbar)
        (activity as? AppCompatActivity)?.supportActionBar?.setDisplayHomeAsUpEnabled(true)
        (activity as? AppCompatActivity)?.supportActionBar?.title = args.forecast.dateTimeText
        binding.toolbar.setNavigationOnClickListener { findNavController().navigateUp() }
    }

    private fun displayForecastDetails(forecast: ForecastItem) {
        binding.humidityTextView.text = getString(R.string.humidity_format, forecast.humidity)
        binding.windSpeedTextView.text = getString(R.string.wind_speed_format, forecast.windSpeed)
        binding.pressureTextView.text = getString(R.string.pressure_format, forecast.pressure)
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
