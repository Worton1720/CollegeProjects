package com.example.collegeapp.features.list.presentation.ui

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.appcompat.app.AppCompatActivity
import androidx.fragment.app.Fragment
import androidx.navigation.fragment.findNavController
import androidx.recyclerview.widget.LinearLayoutManager
import com.example.collegeapp.databinding.FragmentListBinding
import com.example.collegeapp.features.list.presentation.viewmodel.ListViewModel
import org.koin.androidx.viewmodel.ext.android.viewModel

class ListFragment : Fragment() {

    private var _binding: FragmentListBinding? = null
    private val binding get() = _binding!!

    private val listViewModel: ListViewModel by viewModel()
    private lateinit var favoriteCityAdapter: FavoriteCityAdapter

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = FragmentListBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        (activity as? AppCompatActivity)?.setSupportActionBar(binding.toolbar)

        setupRecyclerView()
        observeViewModel()

        // TODO: Get the real user ID
        listViewModel.getFavoriteCities(1)
    }

    private fun setupRecyclerView() {
        favoriteCityAdapter = FavoriteCityAdapter { city ->
            val action = ListFragmentDirections.actionListFragmentToWeatherFragment(city)
            findNavController().navigate(action)
        }
        binding.recyclerView.apply {
            adapter = favoriteCityAdapter
            layoutManager = LinearLayoutManager(requireContext())
        }
    }

    private fun observeViewModel() {
        listViewModel.favoriteCities.observe(viewLifecycleOwner) { cities ->
            favoriteCityAdapter.submitList(cities)
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
