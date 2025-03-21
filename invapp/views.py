from rest_framework.decorators import api_view
from rest_framework.response import Response
from django.contrib.auth import authenticate
from rest_framework_simplejwt.tokens import RefreshToken

from .serializers import UserSerializer
from django.shortcuts import render, redirect
from django.contrib.auth.models import User
from django.contrib import messages

@api_view(['POST'])
def login(request):
    email = request.data.get('email')
    password = request.data.get('password')

    user = authenticate(request,email=email, password=password)  # Now it works with email

    if user is not None:
        refresh = RefreshToken.for_user(user)
        user_data = UserSerializer(user).data
        return Response({
            "message": "Login successful",
            "token": str(refresh.access_token),
            "refresh": str(refresh),
            "user": user_data
        })
    else:
        return Response({"message": "Invalid credentials"}, status=400)

@api_view(['GET'])
def protected_view(request):
    return Response({"message": f"Hello, {request.user.username}"})

@api_view(['POST'])
def register(request):
    password = request.data.get('password')
    email = request.data.get('email')
    full_name = request.data.get('full_name')
    
    if User.objects.filter(email=email).exists():
        return Response({"message": "User already exists"}, status=400)
    
    user = User.objects.create_user(username=full_name , password=password, email=email)
    user.save()
    
    refresh = RefreshToken.for_user(user)
    user_data = UserSerializer(user).data
    
    return Response({
        "message": "Registration successful",
        "token": str(refresh.access_token),
        "refresh": str(refresh),
        "user": user_data
    })

