

from django.urls import path
from invapp.views import *

urlpatterns = [
    path('login/', login, name='login'),
    path('register/', register, name='register'),
    path('protected/', protected_view, name='protected'),
    path('categories/', categories, name='categories'), 
    path('users/', users, name='users'),  
    ]
    





