from rest_framework.decorators import api_view
from rest_framework.response import Response
from django.contrib.auth import get_user_model
from django.contrib.auth import authenticate
from rest_framework.status import HTTP_400_BAD_REQUEST, HTTP_201_CREATED
from rest_framework_simplejwt.tokens import RefreshToken
from .serializers import UserSerializer
from django.shortcuts import render, redirect
from django.contrib.auth.models import User
from django.contrib import messages

# for customeruser to see a data 
from .serializers import CustomUserSerializer 
User = get_user_model()  # Get custom user model if extended


# for function based views for categories
from rest_framework import status
from .models import Categories
from .serializers import CategorySerializer

# logic for user registration {login}
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

# logic for user {protected view}
@api_view(['GET'])
def protected_view(request):
    return Response({"message": f"Hello, {request.user.username}"})


# logic for user registration {register}
@api_view(['POST'])
def register(request):
    try:
        password = request.data.get('password')
        email = request.data.get('email')
        full_name = request.data.get('full_name')
        mobile_no = request.data.get('mobile_no')

        if not password or not email or not full_name:
            return Response({"message": "All fields are required"}, status=404)

        if User.objects.filter(email=email).exists():
            return Response({"message": "User with same email already exists"}, status=404)

        user = User.objects.create_user(username=full_name, password=password, email=email, mobile_no=mobile_no)
        if not user:
            return Response({"message": "Not Registered"}, status=404)

        user.save()

        refresh = RefreshToken.for_user(user)
        user_data = UserSerializer(user).data

        return Response({
            "message": "User Created Successfully!",
            "token": str(refresh.access_token),
            "refresh": str(refresh),
            "user": user_data
        }, status=404)

    except Exception as e:
        return Response({"message": "Not Registered", "error": str(e)}, status=404)
    

# logic for add categories {add_category}
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
    
    # return list like users
    # add function for production
    
    
    

@api_view(['GET'])
def users(request):
    users = User.objects.all()  # Fetch all users
    serializer = CustomUserSerializer(users, many=True)  #  Use correct serializer

    #  Loop through serialized data (if you want to format it manually)
    data = []
    for user in serializer.data:
        data.append({
            "id": user["id"],
            "username": user["username"],
            "email": user["email"],
            "is_active":user["is_active"],
            "mobile_no":user["mobile_no"]
        })

    return Response({"users": data})   # Return formatted data


@api_view(['POST'])
def user_management(request):
    action = request.data.get('action')
    user_id = request.data.get('user_id')

    if not action or not user_id:
        return Response({"message": "Missing action or user_id"}, status=400)

    try:
        user = User.objects.get(id=user_id)

        if action == 'active':
            user.is_active = 1
            user.save()
            return Response({"message": "User activated successfully"}, status=200)

        elif action == 'inactive':
            user.is_active = 0
            user.save()
            return Response({"message": "User deactivated successfully"}, status=200)

        else:
            return Response({"message": "Invalid action"}, status=400)

    except User.DoesNotExist:
        return Response({"message": "User not found"}, status=404)

    except Exception as e:
        return Response({"message": f"Error: {str(e)}"}, status=500)
