import { IsEmail, IsEnum, IsNotEmpty } from 'class-validator';

export class RegisterDto {
  @IsEmail()
  email!: string;

  @IsNotEmpty()
  password!: string;

  @IsEnum(['AUTHOR', 'EDITOR'])
  role!: 'AUTHOR' | 'EDITOR';

  @IsNotEmpty()
  pseudonym!: string;

  @IsNotEmpty()
  firstName!: string;

  @IsNotEmpty()
  lastName!: string;

  middleName?: string;

  @IsNotEmpty()
  birthDate!: string;

  @IsNotEmpty()
  phone!: string;
}