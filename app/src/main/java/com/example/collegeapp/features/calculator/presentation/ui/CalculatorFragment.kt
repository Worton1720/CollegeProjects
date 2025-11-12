package com.example.collegeapp.features.calculator.presentation.ui

import CalculatorViewModel
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import com.example.collegeapp.R
import com.example.collegeapp.databinding.FragmentCalculatorBinding
import org.koin.androidx.viewmodel.ext.android.viewModel

class CalculatorFragment : Fragment() {

    private var _binding: FragmentCalculatorBinding? = null
    private val binding get() = _binding!!

    private val calculatorViewModel: CalculatorViewModel by viewModel()

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = FragmentCalculatorBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        setupButtons()
        observeViewModel()
    }

    private fun setupButtons() {
        // Цифры
        binding.button0.setOnClickListener { calculatorViewModel.onInput("0") }
        binding.button1.setOnClickListener { calculatorViewModel.onInput("1") }
        binding.button2.setOnClickListener { calculatorViewModel.onInput("2") }
        binding.button3.setOnClickListener { calculatorViewModel.onInput("3") }
        binding.button4.setOnClickListener { calculatorViewModel.onInput("4") }
        binding.button5.setOnClickListener { calculatorViewModel.onInput("5") }
        binding.button6.setOnClickListener { calculatorViewModel.onInput("6") }
        binding.button7.setOnClickListener { calculatorViewModel.onInput("7") }
        binding.button8.setOnClickListener { calculatorViewModel.onInput("8") }
        binding.button9.setOnClickListener { calculatorViewModel.onInput("9") }
        binding.buttonDot.setOnClickListener { calculatorViewModel.onInput(".") }

        // Операторы
        binding.buttonPlus.setOnClickListener { calculatorViewModel.onInput("+") }
        binding.buttonMinus.setOnClickListener { calculatorViewModel.onInput("-") }
        binding.buttonMultiply.setOnClickListener { calculatorViewModel.onInput("*") }
        binding.buttonDivide.setOnClickListener { calculatorViewModel.onInput("/") }

        // Специальные кнопки
        binding.buttonEquals.setOnClickListener {
            calculatorViewModel.onCalculate(
                decimalFormat = getString(R.string.calculator_decimal_format),
                errorEmptyExpression = getString(R.string.calculator_error_empty_expression),
                errorDivisionByZero = getString(R.string.calculator_error_division_by_zero),
                errorInvalidExpression = getString(R.string.calculator_error_invalid_expression),
                errorCalculationFailed = getString(R.string.calculator_error_calculation_failed),
                errorUnknown = getString(R.string.calculator_error_unknown)
            )
        }
        binding.buttonClear.setOnClickListener { calculatorViewModel.onClear() }
    }

    private fun observeViewModel() {
        calculatorViewModel.state.observe(viewLifecycleOwner) { state ->
            binding.displayTextView.text = state.displayText
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
