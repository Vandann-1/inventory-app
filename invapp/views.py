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
    username = request.data.get('username')
    password = request.data.get('password')

    user = authenticate(username=username, password=password)
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
def registerv(request):
    username = request.data.get('username')
    password = request.data.get('password')
    email = request.data.get('email')
    
    if User.objects.filter(username=username).exists():
        return Response({"message": "Username already exists"}, status=400)
    
    user = User.objects.create_user(username=username, password=password, email=email)
    user.save()
    
    refresh = RefreshToken.for_user(user)
    user_data = UserSerializer(user).data
    
    return Response({
        "message": "Registration successful",
        "token": str(refresh.access_token),
        "refresh": str(refresh),
        "user": user_data
    })

