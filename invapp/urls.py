

from django.urls import path
from invapp.views import *

urlpatterns = [
    path('login/', login, name='login'),
    path('register/', register, name='register'),
    path('categories/', categories, name='categories'), 
    path('users/', users, name='users'),  # To get users list & make user active & Inactive
    ]
    





