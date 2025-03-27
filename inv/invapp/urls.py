

from django.urls import path
from invapp.views import *

urlpatterns = [
    path('login/', login, name='login'),
    path('register/', register, name='register'),
    path('categories/', categories, name='categories'), 
    path('users/', users, name='users'),  # To get users list & make user active & Inactive
    path('user_management/', user_management, name='user_management'),  
    path('bulk_delete/', bulk_delete, name='bulk_delete'),     # To delete multiple users
    path('product/',product_list,name="product_list")
]





