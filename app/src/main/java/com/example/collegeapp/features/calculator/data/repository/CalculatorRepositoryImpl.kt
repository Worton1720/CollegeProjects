package com.example.collegeapp.features.calculator.data.repository

import com.example.collegeapp.features.calculator.domain.model.CalculationError
import com.example.collegeapp.features.calculator.domain.model.CalculationResult
import com.example.collegeapp.features.calculator.domain.repository.CalculatorRepository
import net.objecthunter.exp4j.ExpressionBuilder
import java.text.DecimalFormat

/**
 * Реализация репозитория калькулятора (Data слой)
 * Использует библиотеку exp4j для вычисления математических выражений
 */
class CalculatorRepositoryImpl : CalculatorRepository {

    override fun calculate(expression: String, decimalFormat: String): CalculationResult {
        return try {
            // Заменяем символы × и ÷ на * и /
            val normalizedExpression = expression
                .replace("×", "*")
                .replace("÷", "/")
                .replace(",", ".")

            // Проверка на пустое выражение после нормализации
            if (normalizedExpression.isBlank()) {
                return CalculationResult.Error(CalculationError.EMPTY_EXPRESSION)
            }

            // Создаём и вычисляем выражение с помощью exp4j
            val exp = ExpressionBuilder(normalizedExpression).build()
            val result = exp.evaluate()

            // Проверка на бесконечность и NaN
            when {
                result.isInfinite() -> CalculationResult.Error(CalculationError.DIVISION_BY_ZERO)
                result.isNaN() -> CalculationResult.Error(CalculationError.INVALID_EXPRESSION)
                else -> {
                    val decimalFormatInstance = DecimalFormat(decimalFormat)
                    CalculationResult.Success(decimalFormatInstance.format(result))
                }
            }
        } catch (e: ArithmeticException) {
            CalculationResult.Error(CalculationError.CALCULATION_FAILED, e.message)
        } catch (e: IllegalArgumentException) {
            CalculationResult.Error(CalculationError.INVALID_EXPRESSION)
        } catch (e: Exception) {
            CalculationResult.Error(CalculationError.UNKNOWN, e.message)
        }
    }
}
