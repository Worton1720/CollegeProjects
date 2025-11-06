package com.example.collegeapp.features.calculator.domain.usecase

import com.example.collegeapp.features.calculator.domain.model.CalculationResult
import com.example.collegeapp.features.calculator.domain.repository.CalculatorRepository

/**
 * UseCase для вычисления математического выражения
 * Инкапсулирует бизнес-логику вычислений
 */
class CalculateExpressionUseCase(
    private val repository: CalculatorRepository
) {

    /**
     * Выполнить UseCase - вычислить выражение
     * @param expression Математическое выражение
     * @return CalculationResult с результатом или ошибкой
     */
    operator fun invoke(expression: String): CalculationResult {
        // Валидация пустого выражения
        if (expression.isBlank()) {
            return CalculationResult.Error("Выражение пустое")
        }

        return repository.calculate(expression)
    }
}
