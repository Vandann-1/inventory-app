from django.contrib.auth.backends import ModelBackend
from django.contrib.auth.models import User


# For login using email
class EmailBackend(ModelBackend):
    def authenticate(self, request, email=None, password=None, **kwargs):
        try:
            user = User.objects.get(email=email)  # Look up user by email
            if user.check_password(password):  # Validate password
                return user
        except User.DoesNotExist:
            return None
