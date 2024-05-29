class VALUES {
    static email_phone_string(val) {
        // Регулярное выражение для проверки телефонного номера (примеры: 123-456-7890, (123) 456-7890, +1-123-456-7890)
        const phoneRegex = /^(?:\+?\d{1,3}[-.\s]?)?(?:\(\d{1,4}\)|\d{1,4})[-.\s]?\d{1,4}[-.\s]?\d{1,4}[-.\s]?\d{1,9}$/;

        // Регулярное выражение для проверки email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (phoneRegex.test(val)) {
            return 'phone';
        } else if (emailRegex.test(val)) {
            return 'email';
        } else {
            return 'login';
        }
    }
}