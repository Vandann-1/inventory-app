

from django.urls import path
from invapp.views import *

urlpatterns = [
    path('login/', login, name='login'),
    path('register/', registerv, name='register'),
    path('protected/', protected_view, name='protected'),
]
