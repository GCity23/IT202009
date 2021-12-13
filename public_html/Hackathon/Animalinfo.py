import random

print("Hello, Welcome to our website. Here, you can learn all about animals!")
userChoice = input("What would you like to do? Trivia or Random Facts?")
lst = ["What is 2*2" , "What does never go down?" , "What is my name?"]
answers = ["4", "age", "Gautham"]
while True:
    if userChoice == "Trivia" or userChoice == "trivia":
        compQuestion = random.choice(lst)
        print(compQuestion)
        compAnswer = input("Answer: ")
        if compAnswer == answers[lst.index(compQuestion)]:
            print("Nice job")
        else:
            print("Better luck next time")
            print("The answer is " + answers[lst.index(compQuestion)])
        userStatus = input("Do you want to keep going?")
        if userStatus == "yes" or userStatus == "Yes":
            continue
        else:
            break

