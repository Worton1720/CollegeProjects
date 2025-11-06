package com.example.collegeapp.features.calculator.domain.model

/**
 * Результат вычисления математического выражения
 * Sealed class для безопасной обработки результата
 */
sealed class CalculationResult {

    /**
     * Успешное вычисление
     */
    data class Success(val result: String) : CalculationResult()

    /**
     * Ошибка при вычислении
     */
    data class Error(val error: CalculationError, val message: String? = null) : CalculationResult()
}
