package com.example.collegeapp.features.weather.presentation.ui

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.view.Menu
import android.view.MenuInflater
import android.view.MenuItem
import android.view.inputmethod.InputMethodManager
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.MenuProvider
import androidx.fragment.app.Fragment
import androidx.lifecycle.Lifecycle
import androidx.navigation.fragment.findNavController
import androidx.navigation.fragment.navArgs
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
    private val args: WeatherFragmentArgs by navArgs()

    private var favoriteMenuItem: MenuItem? = null

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

        args.cityName?.let {
            weatherViewModel.fetchWeather(it)
        } ?: weatherViewModel.fetchWeather("Moscow")

        requireActivity().addMenuProvider(object : MenuProvider {
            override fun onCreateMenu(menu: Menu, menuInflater: MenuInflater) {
                menuInflater.inflate(R.menu.weather_menu, menu)
                favoriteMenuItem = menu.findItem(R.id.action_add_to_favorites)
            }

            override fun onMenuItemSelected(menuItem: MenuItem): Boolean {
                return when (menuItem.itemId) {
                    R.id.action_add_to_favorites -> {
                        val state = weatherViewModel.state.value
                        if (state is WeatherState.Success) {
                            // TODO: Get the real user ID
                            weatherViewModel.toggleFavoriteStatus(1, state.weatherForecast.city.name)
                        }
                        true
                    }
                    else -> false
                }
            }
        }, viewLifecycleOwner, Lifecycle.State.RESUMED)
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
            val action = WeatherFragmentDirections.actionWeatherFragmentToDetailWeatherFragment(forecast)
            findNavController().navigate(action)
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

        weatherViewModel.isFavorite.observe(viewLifecycleOwner) { isFavorite ->
            favoriteMenuItem?.setIcon(
                if (isFavorite) R.drawable.ic_star_filled else R.drawable.ic_star_border
            )
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
