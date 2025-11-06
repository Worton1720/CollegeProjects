import android.content.Context
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import com.example.collegeapp.R
import com.example.collegeapp.features.calculator.domain.model.CalculationError
import com.example.collegeapp.features.calculator.domain.model.CalculationResult
import com.example.collegeapp.features.calculator.domain.usecase.CalculateExpressionUseCase
import com.example.collegeapp.features.calculator.presentation.state.CalculatorState

/**
 * ViewModel для калькулятора
 * Управляет состоянием UI и выполняет вычисления через UseCase
 */
class CalculatorViewModel(
    private val calculateExpressionUseCase: CalculateExpressionUseCase,
    private val context: Context
) : ViewModel() {

    private val _state = MutableLiveData(CalculatorState())
    val state: LiveData<CalculatorState> = _state

    /**
     * Добавить символ к текущему выражению
     */
    fun onInput(input: String) {
        val currentState = _state.value ?: CalculatorState()

        // Если предыдущее состояние было ошибкой, очищаем
        if (currentState.isError) {
            _state.value = CalculatorState(
                displayText = input,
                currentExpression = input
            )
            return
        }

        val newExpression = if (currentState.displayText == "0") {
            input
        } else {
            currentState.currentExpression + input
        }

        _state.value = currentState.copy(
            displayText = newExpression,
            currentExpression = newExpression
        )
    }

    /**
     * Очистить калькулятор
     */
    fun onClear() {
        _state.value = CalculatorState()
    }

    /**
     * Вычислить текущее выражение
     */
    fun onCalculate() {
        val currentState = _state.value ?: return
        val expression = currentState.currentExpression

        if (expression.isBlank()) {
            return
        }

        val decimalFormat = context.getString(R.string.calculator_decimal_format)
        when (val result = calculateExpressionUseCase(expression, decimalFormat)) {
            is CalculationResult.Success -> {
                _state.value = currentState.copy(
                    displayText = result.result,
                    currentExpression = result.result,
                    isError = false
                )
            }
            is CalculationResult.Error -> {
                _state.value = currentState.copy(
                    displayText = mapErrorToString(result.error, result.message),
                    isError = true
                )
            }
        }
    }

    private fun mapErrorToString(error: CalculationError, message: String?): String {
        return when (error) {
            CalculationError.EMPTY_EXPRESSION -> context.getString(R.string.calculator_error_empty_expression)
            CalculationError.DIVISION_BY_ZERO -> context.getString(R.string.calculator_error_division_by_zero)
            CalculationError.INVALID_EXPRESSION -> context.getString(R.string.calculator_error_invalid_expression)
            CalculationError.CALCULATION_FAILED -> context.getString(R.string.calculator_error_calculation_failed, message ?: "")
            CalculationError.UNKNOWN -> context.getString(R.string.calculator_error_unknown, message ?: "")
        }
    }
}
