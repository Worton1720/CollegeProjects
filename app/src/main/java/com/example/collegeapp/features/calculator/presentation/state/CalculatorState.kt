package com.example.collegeapp.features.calculator.presentation.state

/**
 * Состояние UI для экрана калькулятора
 */
data class CalculatorState(
    val displayText: String = "0",
    val currentExpression: String = "",
    val isError: Boolean = false
)
