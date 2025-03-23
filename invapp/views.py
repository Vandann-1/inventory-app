from rest_framework.decorators import api_view
from rest_framework.response import Response
from django.contrib.auth import authenticate
from rest_framework.status import HTTP_400_BAD_REQUEST, HTTP_201_CREATED
from rest_framework_simplejwt.tokens import RefreshToken
from .serializers import UserSerializer
from django.shortcuts import render, redirect
from django.contrib.auth.models import User
from django.contrib import messages

from .serializers import CustomUserSerializer 


# for function based views for categories
from rest_framework import status
from .models import Categories
from .serializers import CategorySerializer

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
    try:
        password = request.data.get('password')
        email = request.data.get('email')
        full_name = request.data.get('full_name')

        if not password or not email or not full_name:
            return Response({"message": "All fields are required"}, status=404)

        if User.objects.filter(email=email).exists():
            return Response({"message": "User with same email already exists"}, status=404)

        user = User.objects.create_user(username=full_name, password=password, email=email)
        if not user:
            return Response({"message": "Not Registered"}, status=404)

        user.save()

        refresh = RefreshToken.for_user(user)
        user_data = UserSerializer(user).data

        return Response({
            "message": "Registration successful",
            "token": str(refresh.access_token),
            "refresh": str(refresh),
            "user": user_data
        }, status=404)

    except Exception as e:
        return Response({"message": "Not Registered", "error": str(e)}, status=404)
    


@api_view(['GET', 'POST'])
def categories(request):
    if request.method == 'GET':
        # Retrieve all categories
        categories = Categories.objects.all()
        serializer = CategorySerializer(categories, many=True)
        return Response(serializer.data)

    elif request.method == 'POST':
        #  Create a new category
        serializer = CategorySerializer(data=request.data)
        if serializer.is_valid():
            serializer.save()
            return Response(serializer.data, status=status.HTTP_201_CREATED)
        return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)
    
    

@api_view(['GET'])
def users(request):
    users = User.objects.all()  # Fetch all users
    serializer = CustomUserSerializer(users, many=True)  #  Use correct serializer

    # ✅ Loop through serialized data (if you want to format it manually)
    data = []
    for user in serializer.data:
        data.append({
            "id": user["id"],
            "username": user["username"],
            "email": user["email"],
        
        })

    return Response({"users": data})   # Return formatted data

