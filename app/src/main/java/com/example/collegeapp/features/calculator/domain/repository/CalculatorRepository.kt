package com.example.collegeapp.features.calculator.domain.repository

import com.example.collegeapp.features.calculator.domain.model.CalculationResult

/**
 * Интерфейс репозитория калькулятора (Domain слой)
 * Определяет контракт для вычисления математических выражений
 */
interface CalculatorRepository {

    /**
     * Вычислить математическое выражение
     * @param expression Строка с математическим выражением (например: "2+3*4")
     * @return CalculationResult с результатом или ошибкой
     */
    fun calculate(expression: String): CalculationResult
}
