# Generated by Django 5.1.7 on 2025-03-27 16:07

import invapp.models
from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('invapp', '0007_supplier_product_supplier'),
    ]

    operations = [
        migrations.AddField(
            model_name='categories',
            name='category_code',
            field=models.CharField(default=invapp.models.generate_random_text, max_length=500, unique=True),
        ),
    ]
