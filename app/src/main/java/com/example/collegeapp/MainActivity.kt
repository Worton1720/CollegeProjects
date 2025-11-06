package com.example.collegeapp

import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import androidx.fragment.app.Fragment
import com.example.collegeapp.databinding.ActivityMainBinding
import com.example.collegeapp.features.calculator.presentation.ui.CalculatorFragment
import com.example.collegeapp.features.weather.presentation.ui.WeatherFragment

class MainActivity : AppCompatActivity() {

    private lateinit var binding: ActivityMainBinding

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // Устанавливаем начальный фрагмент (Погода)
        if (savedInstanceState == null) {
            loadFragment(WeatherFragment())
        }

        setupBottomNavigation()
    }

    private fun setupBottomNavigation() {
        binding.bottomNavigationView.setOnItemSelectedListener { item ->
            when (item.itemId) {
                R.id.nav_weather -> {
                    loadFragment(WeatherFragment())
                    true
                }
                R.id.nav_calculator -> {
                    loadFragment(CalculatorFragment())
                    true
                }
                else -> false
            }
        }
    }

    private fun loadFragment(fragment: Fragment) {
        supportFragmentManager.beginTransaction()
            .replace(R.id.fragmentContainer, fragment)
            .commit()
    }
}
